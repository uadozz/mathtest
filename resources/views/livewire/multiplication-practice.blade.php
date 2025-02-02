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
            margin: 0 auto;
        }
        h1, h2 {
            text-align: center;
        }
        /* Responsive font sizes */
        .problem, .feedback, .score {
            font-size: 1.8rem;
        }
        @media (min-width: 768px) {
            .problem, .feedback, .score {
                font-size: 2.3rem;
            }
        }
        @media (min-width: 1024px) {
            .problem, .feedback, .score {
                font-size: 2.8rem;
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
    </style>

    @if(!$hasStarted)
        <div class="container">
            <h1>Вибери варіанти для перевірки</h1>
            @if(session()->has('error'))
                <div style="color: red;">{{ session('error') }}</div>
            @endif
            <form wire:submit.prevent="startPractice">
                <div style="margin-bottom: 15px;">
                    @foreach(range($minMultiplier, 9) as $base)
                        <label style="margin-right: 10px;">
                            <input type="checkbox" wire:model="selectedBases" value="{{ $base }}">
                            {{ $base }}
                        </label>
                    @endforeach
                </div>
                <button type="submit">Почати перевірку</button>
            </form>
        </div>
    @else
        <!-- Fixed Score Display -->
        <div class="score">
            <strong>Результат:</strong> <span style="color: green;">Правильно: {{ $correctCount }}</span> | <span style="color: red;">Неправильно: {{ $incorrectCount }}</span>
        </div>

        <div class="container">
            <h2>Приклади</h2>

            @if($practiceComplete)
                <div class="feedback" style="text-align: center; color: blue;">
                    Ти завершив розвʼязаня усіх прикладів! Молодець!
                </div>
            @endif

            <ul class="problem-list" style="list-style: none; padding: 0;">
                @foreach($problems as $index => $problem)
                    <li class="problem">
                        @if($loop->last && $problem['userAnswer'] === null && !$practiceComplete)
                            <!-- Current unsolved problem -->
                            <span class="question">
                                {{ $problem['base'] }} x {{ $problem['multiplier'] }} = 
                            </span>
                            <form wire:submit.prevent="submitAnswer" style="display: inline;">
                                <span x-data x-init="$nextTick(() => { $refs.answer.focus() })" style="display:inline-block;">
                                    <input type="number" wire:model.defer="currentAnswer" x-ref="answer" autofocus>
                                </span>
                                <button type="submit">ОК</button>
                            </form>
                        @else
                            <!-- Solved problem -->
                            @if($problem['result'] === 'correct')
                                <span class="correct">
                                    {{ $problem['base'] }} x {{ $problem['multiplier'] }} = {{ $problem['correctAnswer'] }}
                                </span>
                            @else
                                <span class="incorrect">
                                    {{ $problem['base'] }} x {{ $problem['multiplier'] }} = 
                                    <del>{{ $problem['userAnswer'] }}</del> {{ $problem['correctAnswer'] }}
                                </span>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

