<div class="collapse-pomodoro show" id="collapsePomodoro">
    <h3 class="section-title">Pomodoro Timer</h3>
    <div class="pomodoro-card d-flex flex-column px-3">
        <div class="d-flex flex-row justify-content-between gap-4 align-items-center">
            <div class="d-flex flex-column">
                <div id="mainTimerDisplay" class="main-timer-display text-center">25:00</div>
                <div class="control-buttons flex justify-center mb-4">
                    <button id="startBtn" class="pomodoro-buttons">START</button>
                    <button id="pauseBtn" class="pomodoro-buttons" disabled>PAUSE</button>
                    <button id="resetBtn" class="pomodoro-buttons">RESET</button>
                </div>
            </div>
            <div class="d-flex flex-column">
                <div class="d-flex flex-column gap-2">
                    <div class="pomodoro-categories d-flex flex-row align-items-center" id="focusModeContainer">
                        <button class="pomodoro-arrow set-btn" data-mode="focus">Focus Session</button>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="m15.146 12.354-5.792 5.792a.5.5 0 0 1-.854-.353V6.207a.5.5 0 0 1 .854-.353l5.792 5.792a.5.5 0 0 1 0 .708Z"></path></svg></a>
                    </div>
                    <div class="pomodoro-categories d-flex flex-row align-items-center" id="shortBreakModeContainer">
                        <button class="pomodoro-arrow set-btn" data-mode="shortBreak">Short Break</button>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="m15.146 12.354-5.792 5.792a.5.5 0 0 1-.854-.353V6.207a.5.5 0 0 1 .854-.353l5.792 5.792a.5.5 0 0 1 0 .708Z"></path></svg></a>
                    </div>
                    <div class="pomodoro-categories d-flex flex-row align-items-center" id="longBreakModeContainer">
                        <button class="pomodoro-arrow set-btn" data-mode="longBreak">Long Break</button>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="m15.146 12.354-5.792 5.792a.5.5 0 0 1-.854-.353V6.207a.5.5 0 0 1 .854-.353l5.792 5.792a.5.5 0 0 1 0 .708Z"></path></svg></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="music-player-container">
            <h3 class="music-player-title">Relaxing Music</h3>
            <div class="music-player">
                <div class="album-art-wrapper">
                    <img id="albumArt" src="https://via.placeholder.com/50" alt="Album Art" class="album-art">
                    <button id="playPauseButton" class="tiny-play-pause-button">
                        <svg id="playIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="play-icon">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <svg id="pauseIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="pause-icon hidden">
                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                        </svg>
                    </button>
                </div>
                <div class="song-info">
                    <div id="songTitle" class="song-title">Title Of Music</div>
                    <div id="songDuration" class="song-duration">00:00</div>
                </div>
                <div class="controls-compact">
                    <button id="prevButton" class="control-button-compact">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11 18V6l-8 6zM15.5 18V6l-8 6z"/>
                        </svg>
                    </button>
                    <button id="nextButton" class="control-button-compact">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13 6v12l8-6zM8.5 6v12l8-6z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <audio id="audioPlayer" src="" preload="auto"></audio>
        </div>
    </div>
</div>
<button class="btn" style="width: fit-content;margin-top: -28px;margin-right: -15px;z-index: 1;border: 0;" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePomodoro" aria-expanded="true" aria-controls="collapsePomodoro">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30"><path d="M1 12C1 5.925 5.925 1 12 1s11 4.925 11 11-4.925 11-11 11S1 18.075 1 12Zm11.575-4.75a.825.825 0 1 0-1.65 0v5.5c0 .296.159.57.416.716l3.5 2a.825.825 0 0 0 .818-1.432l-3.084-1.763Z"></path></svg>
</button>
