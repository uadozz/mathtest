<?php

namespace App\Livewire;

use Livewire\Component;

class MultiplicationPractice extends Component
{
    public $hasStarted = false;
    public $minMultiplier = 2;        // Minimum multiplier used for problem generation
    public $selectedOperations = ['multiplication', 'division']; // Default operation(s) selected
    public $selectedBases = [2,3,4,5,6,7,8,9];  // Initially all bases are selected
    public $problems = [];            // Problems already answered (with user answers)
    public $remainingProblems = [];   // Pre-generated, shuffled list of remaining problems
    public $currentAnswer = '';
    public $correctCount = 0;
    public $incorrectCount = 0;
    public $practiceComplete = false; // Flag when no new problem should be generated

    // On mount, load the state from the session (if available)
    public function mount()
    {
        if (session()->has('multiplication_state')) {
            $state = session('multiplication_state');
            $this->hasStarted        = $state['hasStarted'] ?? false;
            $this->selectedOperations= $state['selectedOperations'] ?? ['multiplication', 'division'];
            $this->selectedBases     = $state['selectedBases'] ?? [2,3,4,5,6,7,8,9];
            $this->problems          = $state['problems'] ?? [];
            $this->remainingProblems = $state['remainingProblems'] ?? [];
            $this->currentAnswer     = $state['currentAnswer'] ?? '';
            $this->correctCount      = $state['correctCount'] ?? 0;
            $this->incorrectCount    = $state['incorrectCount'] ?? 0;
            $this->practiceComplete  = $state['practiceComplete'] ?? false;
        }
    }

    // Save the component's state into the session
    protected function saveState()
    {
        session([
            'multiplication_state' => [
                'hasStarted'        => $this->hasStarted,
                'selectedOperations'=> $this->selectedOperations,
                'selectedBases'     => $this->selectedBases,
                'problems'          => $this->problems,
                'remainingProblems' => $this->remainingProblems,
                'currentAnswer'     => $this->currentAnswer,
                'correctCount'      => $this->correctCount,
                'incorrectCount'    => $this->incorrectCount,
                'practiceComplete'  => $this->practiceComplete,
            ]
        ]);
    }

    public function startPractice()
    {
        if (empty($this->selectedOperations)) {
            session()->flash('error', 'Будь ласка, виберіть принаймні одну операцію.');
            return;
        }
        if (empty($this->selectedBases)) {
            session()->flash('error', 'Будь ласка, виберіть хоча б одне число.');
            return;
        }

        $this->hasStarted = true;
        $this->correctCount = 0;
        $this->incorrectCount = 0;
        $this->problems = [];
        $this->remainingProblems = [];
        $this->practiceComplete = false;

        // Pre-generate all problems according to selected operations, bases, and multipliers
        foreach ($this->selectedOperations as $op) {
            foreach ($this->selectedBases as $base) {
                for ($i = $this->minMultiplier; $i <= 9; $i++) {
                    if ($op === 'multiplication') {
                        $this->remainingProblems[] = [
                            'operation'     => 'multiplication',
                            'base'          => $base,
                            'multiplier'    => $i,
                            'question'      => "$base x $i",
                            'correctAnswer' => $base * $i,
                        ];
                    } elseif ($op === 'division') {
                        // For division, use "dividend ÷ base" where dividend = base * multiplier.
                        $dividend = $base * $i;
                        $this->remainingProblems[] = [
                            'operation'     => 'division',
                            'base'          => $base,
                            'multiplier'    => $i, // this is also the quotient (answer)
                            'question'      => "$dividend ÷ $base",
                            'correctAnswer' => $i,
                        ];
                    }
                }
            }
        }

        // Shuffle the list so the problems appear in random order
        shuffle($this->remainingProblems);

        // Load the first problem
        $this->addProblem();
        $this->saveState();
    }

    public function addProblem()
    {
        if (empty($this->remainingProblems)) {
            // No more problems available—mark practice as complete.
            $this->practiceComplete = true;
            $this->saveState();
            return;
        }

        // Retrieve the next problem from the shuffled list
        $problem = array_pop($this->remainingProblems);
        // Initialize fields for user answer and result
        $problem['userAnswer'] = null;
        $problem['result'] = null;
        $this->problems[] = $problem;
        $this->currentAnswer = '';
        $this->saveState();
    }

    public function submitAnswer()
    {
        if ($this->practiceComplete) {
            return; // Do nothing if practice is finished
        }

        $index = count($this->problems) - 1;
        if ($index < 0) {
            return;
        }

        $userAnswer = intval($this->currentAnswer);
        $this->problems[$index]['userAnswer'] = $userAnswer;

        if ($userAnswer === $this->problems[$index]['correctAnswer']) {
            $this->problems[$index]['result'] = 'correct';
            $this->correctCount++;
        } else {
            $this->problems[$index]['result'] = 'incorrect';
            $this->incorrectCount++;
        }

        $this->currentAnswer = '';
        $this->addProblem();
        $this->saveState();
    }

    // Finishes the practice so that no new problem is generated.
    public function finishPractice()
    {
        $this->practiceComplete = true;
        $this->saveState();
    }

    // Reset everything and clear session data so the user can start over.
    public function resetPractice()
    {
        session()->forget('multiplication_state');
        $this->hasStarted = false;
        $this->selectedOperations = ['multiplication'];
        $this->selectedBases = [2,3,4,5,6,7,8,9];
        $this->problems = [];
        $this->remainingProblems = [];
        $this->currentAnswer = '';
        $this->correctCount = 0;
        $this->incorrectCount = 0;
        $this->practiceComplete = false;
    }

    public function render()
    {
        return view('livewire.multiplication-practice');
    }
}
