<div>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 30px auto;
        }
        h1, h2 {
            text-align: center;
        }
        /* Responsive font sizes */
        .problem, .feedback, .score {
            font-size: 1.5rem;
        }
        @media (min-width: 768px) {
            .problem, .feedback, .score {
                font-size: 2rem;
            }
        }
        @media (min-width: 1024px) {
            .problem, .feedback, .score {
                font-size: 2.5rem;
            }
        }
        .correct {
            color: green;
        }
        .incorrect {
            color: red;
        }
        .score {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #f5f5f5;
            padding: 10px;
            border-bottom: 1px solid #ccc;
            z-index: 100;
            text-align: center;
        }
        .problem-list {
            margin-top: 60px;
        }
        .problem {
            margin-bottom: 20px;
        }
        input[type="number"] {
            font-size: inherit;
            padding: 5px;
            width: 100px;
        }
        button {
            font-size: inherit;
            padding: 5px 10px;
        }
        .actions {
            text-align: center;
            margin: 20px 0;
        }
    </style>
B
    @if(!$hasStarted)
        <div class="container">
            <h1>Вибери операції та варіанти для перевірки</h1>

            <!-- Operation selection -->
            <div style="margin-bottom: 15px;">
                <label style="margin-right: 10px;">
                    <input type="checkbox" wire:model="selectedOperations" value="multiplication">
                    Множення
                </label>
                <label style="margin-right: 10px;">
                    <input type="checkbox" wire:model="selectedOperations" value="division">
                    Ділення
                </label>
            </div>

            <!-- Bases selection -->
            <div style="margin-bottom: 15px;">
                @foreach(range($minMultiplier, 9) as $base)
                    <label style="margin-right: 10px;">
                        <input type="checkbox" wire:model="selectedBases" value="{{ $base }}">
                        {{ $base }}
                    </label>
                @endforeach
            </div>
            <button type="submit" wire:click.prevent="startPractice">Почати перевірку</button>
            @if(session()->has('error'))
                <div style="color: red;">{{ session('error') }}</div>
            @endif
        </div>
    @else
        <!-- Fixed Score Display -->
        <div class="score">
            <strong>Результат:</strong> <span style="color: green;">Правильно: {{ $correctCount }}</span> | <span style="color: red;">Неправильно: {{ $incorrectCount }}</span>
        </div>

        <div class="container">
            @if(!$practiceComplete)
                <div class="actions">
                    <button wire:click="finishPractice">Закінчити</button>
                </div>
            @endif

            @if($practiceComplete)
                <div class="feedback" style="text-align: center; color: blue;">
                    Ти завершив розвʼязаня усіх прикладів! Молодець!
                </div>
                <div class="actions">
                    <button wire:click="resetPractice">Почати знову</button>
                </div>
            @endif

            <h2>Приклади</h2>

            <ul class="problem-list" style="list-style: none; padding: 0;">
                @foreach($problems as $index => $problem)
                    <li class="problem">
                        @if($loop->last && is_null($problem['userAnswer']) && !$practiceComplete)
                            <!-- Current unsolved problem -->
                            <span class="question">
                                {{ $problem['question'] }} =
                            </span>
                            <form wire:submit.prevent="submitAnswer" style="display: inline;">
                                <span x-data x-init="$nextTick(() => { $refs.answer.focus() })" style="display: inline-block;">
                                    <input type="number" wire:model.defer="currentAnswer" x-ref="answer">
                                </span>
                                <button type="submit">ОК</button>
                            </form>
                        @else
                            <!-- Solved problem -->
                            @if($problem['result'] === 'correct')
                                <span class="correct">
                                    {{ $problem['question'] }} = {{ $problem['correctAnswer'] }}
                                </span>
                            @else
                                <span class="incorrect">
                                    {{ $problem['question'] }} = <del>{{ $problem['userAnswer'] }}</del> {{ $problem['correctAnswer'] }}
                                </span>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
