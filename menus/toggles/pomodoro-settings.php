<!-- modal pop up of pomodoro settings -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"  aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Pomodoro Settings</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-center">
                    <div class="card card-body" style="border: none;">
                        <div class="pomodoro-card">
                            <h3 class="section-title">SET POMODORO</h3>
                            <div class="d-flex flex-row justify-content-around align-items-center items-center gap-6 mb-4">
                                <!-- Focus Session -->
                                <div class="mode-button-container d-flex flex-column align-items-center relative" id="focusModeContainer">
                                    <div class="d-flex flex-row align-items-center gap-3">
                                        <span class="time-adjust-arrow arrow-left" data-mode="focus" data-direction="down">-</span>
                                        <div class="mode-circle d-flex flex-column align-items-center" data-mode="focus">
                                            <span id="focusMinDisplay">25</span><span class="text-xl">min</span>
                                        </div>
                                        <span class="time-adjust-arrow arrow-right" data-mode="focus" data-direction="up">+</span>
                                    </div>
                                    <span class="mode-text">Focus Session</span>
                                    <button class="pomodoro-buttons set-btn" data-mode="focus">SET</button>
                                </div>
                                <!-- Short Break -->
                                <div class="mode-button-container d-flex flex-column align-items-center relative" id="shortBreakModeContainer">
                                    <div class="d-flex flex-row align-items-center gap-3">
                                        <span class="time-adjust-arrow arrow-left" data-mode="shortBreak" data-direction="down">-</span>
                                        <div class="mode-circle d-flex flex-column align-items-center" data-mode="shortBreak">
                                            <span id="shortBreakMinDisplay">5</span><span class="text-xl">min</span>
                                        </div>
                                        <span class="time-adjust-arrow arrow-right" data-mode="shortBreak" data-direction="up">+</span>
                                    </div>
                                    <span class="mode-text">Short Break</span>
                                    <button class="pomodoro-buttons set-btn" data-mode="shortBreak">SET</button>
                                </div>
                                <!-- Long Break -->
                                <div class="mode-button-container d-flex flex-column align-items-center relative" id="longBreakModeContainer">
                                    <div class="d-flex flex-row align-items-center gap-3">
                                        <span class="time-adjust-arrow arrow-left" data-mode="longBreak" data-direction="down">-</span>
                                        <div class="mode-circle d-flex flex-column align-items-center" data-mode="longBreak">
                                            <span id="longBreakMinDisplay">15</span><span class="text-xl">min</span>
                                        </div>
                                        <span class="time-adjust-arrow arrow-right" data-mode="longBreak" data-direction="up">+</span>
                                    </div>
                                    <span class="mode-text">Long Break</span>
                                    <button class="pomodoro-buttons set-btn" data-mode="longBreak">SET</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>