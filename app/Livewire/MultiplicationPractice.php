<?php

namespace App\Livewire;

use Livewire\Component;

class MultiplicationPractice extends Component
{
    public $hasStarted = false;
    public $minMultiplier = 2;        // Minimum multiplier
    public $selectedBases = [2,3,4,5,6,7,8,9];       // e.g., [2,3,5]
    public $problems = [];            // Array of problems already asked (with user answers)
    public $remainingProblems = [];   // Pre-generated, shuffled list of remaining problems
    public $currentAnswer = '';
    public $correctCount = 0;
    public $incorrectCount = 0;
    public $practiceComplete = false; // Flag when all problems have been used

    public function startPractice()
    {
        if (empty($this->selectedBases)) {
            session()->flash('error', 'Please select at least one multiplication base.');
            return;
        }
        
        $this->hasStarted = true;
        $this->correctCount = 0;
        $this->incorrectCount = 0;
        $this->problems = [];
        $this->remainingProblems = [];
        $this->practiceComplete = false;
        
        // Pre-generate all problems (multiplier up to 9 for each selected base)
        foreach ($this->selectedBases as $base) {
            for ($i = $this->minMultiplier; $i <= 9; $i++) {
                $this->remainingProblems[] = [
                    'base'          => $base,
                    'multiplier'    => $i,
                    'correctAnswer' => $base * $i,
                ];
            }
        }
        
        // Shuffle the list so problems come in random order
        shuffle($this->remainingProblems);
        
        // Load the first problem
        $this->addProblem();
    }

    public function addProblem()
    {
        if (empty($this->remainingProblems)) {
            // No more new problems available
            $this->practiceComplete = true;
            return;
        }
        
        // Get the next problem from the shuffled list
        $problem = array_pop($this->remainingProblems);
        // Initialize user answer and result
        $problem['userAnswer'] = null;
        $problem['result'] = null;
        $this->problems[] = $problem;
        $this->currentAnswer = '';
    }

    public function submitAnswer()
    {
        if ($this->practiceComplete) {
            return; // do nothing if practice is already complete
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
    }

    public function render()
    {
        return view('livewire.multiplication-practice');
    }
}

