<?php

namespace App\Http\Controllers\Admin;
use App\Notifications\QuestionReviewNotification;
use App\Services\QuestionStatusManager;
use App\Enums\QuestionStatus;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuestionManagementController extends Controller
{
    /* ============================================================
     |  INDEX – LIST + FILTERS (Scalable)
     ============================================================ */
    public function index(Request $request)
    {
        $query = Question::with(['category','answers'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('q')) {
            $query->where('title','like','%'.$request->q.'%');
        }

        $questions  = $query->paginate(50)->withQueryString();
        $categories = Category::orderBy('level')->orderBy('name')->get();

        return view('admin.questions.questionlist', compact(
            'questions','categories'
        ));
    }

    /* ============================================================
     |  CREATE FORM
     ============================================================ */
    public function create()
    {
        return view('admin.questions.editor', [
            'mode'       => 'create',
            'question'   => new Question(),
            'answers'    => collect(),
            'categories' => Category::orderBy('level')->orderBy('name')->get(),
        ]);
    }

    /* ============================================================
     |  STORE – QUESTION + ANSWERS
     |  AUTO → REVIEW
     ============================================================ */
    public function store(Request $request)
    {
        $this->validateQuestion($request);

        // 🔥 CRITICAL FIX – normalize answers index
        $answers = array_values($request->answers);

        DB::transaction(function () use ($request, $answers) {

           
$status = $request->action === 'draft'
    ? QuestionStatus::DRAFT
    : QuestionStatus::REVIEW;

$question = Question::create([
    'title'       => $request->title,
    'slug'        => $this->generateUniqueSlug($request->title),
    'body'        => $request->body,
    'category_id' => $request->category_id,
    'status'      => $status,
    'created_by'  => auth()->id(),
    'is_indexable' => false,
]);

            $this->storeAnswers($question, $answers);
        });

        return redirect()
            ->route('admin.questions.index')
            ->with('success','Question created & sent to review');
    }

    /* ============================================================
     |  EDIT FORM
     ============================================================ */
    public function edit(Question $question)
    {
        return view('admin.questions.editor', [
            'mode'       => 'edit',
            'question'   => $question,
            'answers'    => $question->answers()->orderBy('sort_order')->get(),
            'categories' => Category::orderBy('level')->orderBy('name')->get(),
        ]);
    }

    /* ============================================================
     |  UPDATE – ALWAYS BACK TO REVIEW
     ============================================================ */
    public function update(Request $request, Question $question)
    {
        $this->validateQuestion($request, $question->id);

        // 🔥 FIX
        $answers = array_values($request->answers);

        DB::transaction(function () use ($request, $question, $answers) {

            $question->update([
                'title'        => $request->title,
                'slug'         => $this->generateUniqueSlug($request->title, $question->id),
                'body'         => $request->body,
                'category_id'  => $request->category_id,
                'status'       => 'review',
                'is_indexable' => false,
            ]);

            $question->answers()->delete();

            $this->storeAnswers($question, $answers);
        });

        return back()->with('success','Question updated & sent to review');
    }

    /* ============================================================
     |  DELETE
     ============================================================ */
    public function destroy(Question $question)
    {
        $question->delete();
        return back()->with('success','Question deleted');
    }

    /* ============================================================
     |  BULK ACTIONS – FULLY FIXED
     ============================================================ */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:questions,id',
        ]);

        DB::transaction(function () use ($request) {

            $ids = $request->ids;

            switch ($request->action) {

                case 'publish':
    if (!auth()->user()->isSuperAdmin()) {
        abort(403);
    }

    Question::whereIn('id',$ids)
        ->where('status','approved')
        ->update([
            'status' => 'published',
            'published_at' => now(),
            'is_indexable' => true,
        ]);
break;


                case 'review':
                    Question::whereIn('id',$ids)
                        ->update([
                            'status'=>'review',
                            'is_indexable'=>false
                        ]);
                    break;

                    case 'approve':
    Question::whereIn('id',$ids)
        ->where('status','review')
        ->update(['status'=>'approved']);
    break;

case 'reject':
    Question::whereIn('id',$ids)
        ->where('status','review')
        ->update(['status'=>'rejected']);
    break;

                case 'delete':
                    Question::whereIn('id',$ids)->delete();
                    break;

                case 'change_category':
                    if (!$request->filled('category_id')) {
                        throw new \Exception('Category required for bulk change');
                    }

                    Question::whereIn('id',$ids)
                        ->update([
                            'category_id'=>$request->category_id,
                            'status'=>'review'
                        ]);
                    break;
            }
        });

        return back()->with('success','Bulk action executed successfully');
    }

    /* ============================================================
     |  VALIDATION
     ============================================================ */
    protected function validateQuestion(Request $request, $ignoreId = null)
    {
        $request->validate([
            'title' => [
                'required','min:10',
                Rule::unique('questions','title')->ignore($ignoreId)
            ],
            'body'        => 'required|min:20',
            'category_id' => 'required|exists:categories,id',
            'answers'     => 'required|array|min:1',
            'answers.*.content' => 'required|min:5',
            'answers.*.type'    => 'nullable|in:short,detailed,code,beginner,advanced',
        ]);
    }

    /* ============================================================
     |  ANSWER STORAGE (SAFE)
     ============================================================ */
    protected function storeAnswers(Question $question, array $answers)
    {
        foreach ($answers as $index => $ans) {
            Answer::create([
                'question_id' => $question->id,
                'content'     => $ans['content'],
                'answer_type' => $ans['type'] ?? 'detailed',
                'sort_order'  => $index,
                'status'      => 'review',
            ]);
        }
    }

    /* ============================================================
     |  SEO-SAFE SLUG GENERATOR
     ============================================================ */
    protected function generateUniqueSlug($title, $ignoreId = null)
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (
            Question::where('slug',$slug)
                ->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }


public function approve(Question $question)
{
    QuestionStatusManager::transition(
        $question,
        QuestionStatus::APPROVED,
        auth()->id(),
        auth()->user()->increment('reviews_done')

    );

    $question->creator->notify(
        new QuestionReviewNotification($question, 'approved')
    );

    return back()->with('success','Question approved');
}


public function reject(Request $request, Question $question)
{
    $request->validate(['reason'=>'required|min:5']);

    QuestionStatusManager::transition(
        $question,
        QuestionStatus::REJECTED,
        auth()->id(),
        $request->reason,
        auth()->user()->increment('reviews_done')

    );

    $question->creator->notify(
        new QuestionReviewNotification($question,'rejected',$request->reason)
    );

    return back()->with('success','Question rejected');
}


public function acceptAnswer(Answer $answer)
{
    DB::transaction(function () use ($answer) {

        // reset old accepted
        Answer::where('question_id',$answer->question_id)
            ->update(['is_accepted'=>false]);

        $answer->update([
            'is_accepted' => true,
            'status'      => 'published'
        ]);
    });

    return back()->with('success','Answer accepted');
}

    
}
