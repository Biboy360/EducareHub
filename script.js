document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const focusMinDisplay = document.getElementById('focusMinDisplay');
    const shortBreakMinDisplay = document.getElementById('shortBreakMinDisplay');
    const longBreakMinDisplay = document.getElementById('longBreakMinDisplay');
    const mainTimerDisplay = document.getElementById('mainTimerDisplay');
    const startBtn = document.getElementById('startBtn');
    const pauseBtn = document.getElementById('pauseBtn');
    const resetBtn = document.getElementById('resetBtn');

    const setModeButtons = document.querySelectorAll('.pomodoro-buttons.set-btn');
    const timeAdjustArrows = document.querySelectorAll('.time-adjust-arrow');
    const categoryButtons = document.querySelectorAll('.pomodoro-categories .set-btn');

    // Pomodoro settings (default values)
    let focusTime = parseInt(focusMinDisplay.textContent);
    let shortBreakTime = parseInt(shortBreakMinDisplay.textContent);
    let longBreakTime = parseInt(longBreakMinDisplay.textContent);

    let currentMode = 'focus'; // 'focus', 'shortBreak', 'longBreak'
    let timeLeft = focusTime * 60; // Initial time in seconds
    let timerInterval;
    let isPaused = true;
    let originalFocusTimeWhenStarted = 0; // To store the focus time when the timer actually starts

    // Function to update the main timer display
    function updateTimerDisplay() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        mainTimerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    // Function to set the timer mode
    function setMode(mode) {
        currentMode = mode;
        switch (mode) {
            case 'focus':
                timeLeft = focusTime * 60;
                break;
            case 'shortBreak':
                timeLeft = shortBreakTime * 60;
                break;
            case 'longBreak':
                timeLeft = longBreakTime * 60;
                break;
        }
        updateTimerDisplay();
        resetTimerState(); // Reset timer visual and control state, but keep the mode
        updateActiveCategoryButton(); // Ensure the correct button is highlighted
    }

    // Function to update active category button styling
    function updateActiveCategoryButton() {
        categoryButtons.forEach(button => {
            if (button.dataset.mode === currentMode) {
                button.classList.add('active-mode'); // Add a class for active styling
            } else {
                button.classList.remove('active-mode');
            }
        });
    }

    // Function to reset timer internal state (without changing mode)
    function resetTimerState() {
        clearInterval(timerInterval);
        timerInterval = null;
        isPaused = true;
        startBtn.disabled = false;
        pauseBtn.disabled = true;
        originalFocusTimeWhenStarted = 0; // Reset this when timer state is reset
    }

    // Timer control functions
    function startTimer() {
        if (!timerInterval && isPaused) {
            isPaused = false;
            startBtn.disabled = true;
            pauseBtn.disabled = false;

            if (currentMode === 'focus' && originalFocusTimeWhenStarted === 0) {
                originalFocusTimeWhenStarted = focusTime; // Capture focus time only once when starting a new session
            }

            timerInterval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    timerInterval = null;
                    isPaused = true;
                    startBtn.disabled = false;
                    pauseBtn.disabled = true;

                    // Play a sound or show a notification
                    alert(`${currentMode === 'focus' ? 'Focus Session' : 'Break'} Finished!`);

                    if (currentMode === 'focus') {
                        // Call backend to award points
                        awardPoints(originalFocusTimeWhenStarted);
                        originalFocusTimeWhenStarted = 0; // Reset for the next focus session
                    }

                    // Reset timer to current mode's initial time after completion
                    setMode(currentMode); // This will also re-highlight the current mode
                } else {
                    timeLeft--;
                    updateTimerDisplay();
                }
            }, 1000);
        }
    }

    function pauseTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
            isPaused = true;
            startBtn.disabled = false;
            pauseBtn.disabled = true;
        }
    }

    function resetTimer() {
        resetTimerState(); // Stop timer, reset buttons
        setMode(currentMode); // Reset time to the current mode's set value and update display
    }

    // Event Listeners for adjusting time
    timeAdjustArrows.forEach(arrow => {
        arrow.addEventListener('click', (event) => {
            const mode = event.target.dataset.mode;
            const direction = event.target.dataset.direction;
            let value;

            if (mode === 'focus') {
                value = focusTime;
            } else if (mode === 'shortBreak') {
                value = shortBreakTime;
            } else if (mode === 'longBreak') {
                value = longBreakTime;
            }

            if (direction === 'up') {
                value = value + 1;
            } else {
                value = value - 1;
            }

            // Apply specific min/max constraints
            if (mode === 'focus') {
                value = Math.min(60, Math.max(1, value)); // Max 60 minutes, Min 1 minute
                focusTime = value;
                focusMinDisplay.textContent = value;
            } else if (mode === 'shortBreak') {
                value = Math.max(5, Math.min(1,value)); // Short Break minimum 15 minutes
                shortBreakTime = value;
                shortBreakMinDisplay.textContent = value;
            } else if (mode === 'longBreak') {
                value = Math.max(120, Math.min(1, value)); // Long Break maximum 120 minutes (2 hours)
                longBreakTime = value;
                longBreakMinDisplay.textContent = value;
            }

            if (currentMode === mode) {
                // If the current active mode's time is adjusted, update the main timer
                setMode(mode);
            }
        });
    });

    // Event Listeners for "SET" buttons in pomodoro settings
    setModeButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const mode = event.target.dataset.mode;
            setMode(mode);
        });
    });

    // Event Listeners for category buttons (Focus Session, Short Break, Long Break)
    categoryButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const mode = event.target.dataset.mode;
            setMode(mode);
        });
    });

    // Timer control button event listeners
    startBtn.addEventListener('click', startTimer);
    pauseBtn.addEventListener('click', pauseTimer);
    resetBtn.addEventListener('click', resetTimer);

    // Initial setup
    setMode('focus'); // Set initial mode to Focus Session and update display
    // The call to setMode already includes updateActiveCategoryButton()


    // Backend integration: Award Points
    async function awardPoints(completedFocusMinutes) {
        const pointsPerMinute = 40; // 1000 points for 25 minutes = 40 points/minute
        const pointsToAward = completedFocusMinutes * pointsPerMinute;

        const userId = 'Gerry'; // Replace with dynamic user ID from your session management

        try {
            const response = await fetch('award_points.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `userId=${encodeURIComponent(userId)}&points=${encodeURIComponent(pointsToAward)}&focusMinutes=${encodeURIComponent(completedFocusMinutes)}`,
            });

            const result = await response.json();

            if (result.success) {
                console.log(`Points awarded: ${result.newTotalPoints}. Message: ${result.message}`);
                // Optionally, update a points display on the frontend
            } else {
                console.error('Failed to award points:', result.message);
            }
        } catch (error) {
            console.error('Error sending points to backend:', error);
        }
    }
});

//for the clock

function updateRealTimeClock() {
            const now = new Date(); 
            let hours = now.getHours(); 
            const minutes = now.getMinutes(); 
            const ampm = hours >= 12 ? 'PM' : 'AM'; 
            hours = hours % 12; 
            hours = hours ? hours : 12; 
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;


            document.getElementById('realTimeClockDisplay').textContent = `${hours}:${formattedMinutes} ${ampm}`;
        }

        
        document.addEventListener('DOMContentLoaded', () => {
            updateRealTimeClock();

            setInterval(updateRealTimeClock, 1000);
        });

//for music player
    document.addEventListener('DOMContentLoaded', () => {
    // ... (Your existing Pomodoro Timer JavaScript remains here) ...

    // --- Music Player Elements ---
    const audioPlayer = document.getElementById('audioPlayer');
    const playPauseButton = document.getElementById('playPauseButton');
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const albumArt = document.getElementById('albumArt');
    const songTitle = document.getElementById('songTitle');
    const songDuration = document.getElementById('songDuration');

    // --- Music Playlist with Online URLs ---
    const playlist = [
        {
            title: "Relaxing Piano (Sample 1)",
            artist: "Kevin MacLeod",
            // Royalty-free music from Incompetech, suitable for testing
            src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3",
            // You can use a generic image for album art from an online source too, or a local one if you have it.
            albumArt: "https://via.placeholder.com/50/C8E6C9/000000?text=MP1" // A placeholder image with a light background
        },
        {
            title: "Ambient Chill (Sample 2)",
            artist: "Alexander Nakarada",
            src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3",
            albumArt: "https://via.placeholder.com/50/B2DFDB/000000?text=MP2" // Another placeholder image
        },
        {
            title: "Soft Background (Sample 3)",
            artist: "Scott Buckley",
            src: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3",
            albumArt: "https://via.placeholder.com/50/A7FFEB/000000?text=MP3" // Yet another placeholder image
        }
    ];

    let currentTrackIndex = 0;

    // --- Music Player Functions ---

    function loadTrack(index) {
        const track = playlist[index];
        audioPlayer.src = track.src;
        albumArt.src = track.albumArt;
        songTitle.textContent = track.title;
        songDuration.textContent = '00:00'; // Reset duration display
        audioPlayer.load(); // Load the new track data
        updatePlayPauseIcons(); // Update icons based on current state (usually paused after loading a new track)
    }

    function togglePlayPause() {
        if (audioPlayer.paused) {
            const playPromise = audioPlayer.play();

            if (playPromise !== undefined) {
                playPromise.then(() => {
                    // Playback started successfully
                    updatePlayPauseIcons();
                }).catch(error => {
                    // Autoplay was prevented. This might happen if the user hasn't interacted with the page yet.
                    console.error("Audio playback prevented:", error);
                    // Optionally, you can show a user-friendly message, but an alert can be disruptive.
                    // alert("Autoplay prevented! Please click the play button again.");
                    updatePlayPauseIcons(); // Ensure icons reflect paused state
                });
            }
        } else {
            audioPlayer.pause();
            updatePlayPauseIcons();
        }
    }

    function updatePlayPauseIcons() {
        if (audioPlayer.paused) {
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
        } else {
            playIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');
        }
    }

    function playNextTrack() {
        currentTrackIndex = (currentTrackIndex + 1) % playlist.length;
        loadTrack(currentTrackIndex);
        audioPlayer.play(); // Attempt to auto-play next track
        updatePlayPauseIcons();
    }

    function playPrevTrack() {
        currentTrackIndex = (currentTrackIndex - 1 + playlist.length) % playlist.length;
        loadTrack(currentTrackIndex);
        audioPlayer.play(); // Attempt to auto-play previous track
        updatePlayPauseIcons();
    }

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
    }

    // --- Music Player Event Listeners ---

    playPauseButton.addEventListener('click', togglePlayPause);
    prevButton.addEventListener('click', playPrevTrack);
    nextButton.addEventListener('click', playNextTrack);

    // Update song duration when metadata is loaded
    audioPlayer.addEventListener('loadedmetadata', () => {
        songDuration.textContent = formatTime(audioPlayer.duration);
    });

    // Auto-play next track when current track ends
    audioPlayer.addEventListener('ended', playNextTrack);

    // Initial load of the first track
    loadTrack(currentTrackIndex);
});

// for menu tab
document.addEventListener('DOMContentLoaded', function() {

    const menuButtons = document.querySelectorAll('.menus');
    const mainContentArea = document.querySelector('.main-content');

    function loadContent(panelFileName) {

        const filePath = 'contents_panels/' + panelFileName;

        fetch(filePath)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status} for ${filePath}`);
                }
                return response.text();
            })
            .then(html => {
                mainContentArea.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading content:', error);
                mainContentArea.innerHTML = '<div class="alert alert-danger" role="alert">Error loading content. Please try again or contact support.</div>';
            });
    }

    menuButtons.forEach(button => {
        button.addEventListener('click', function() {
            menuButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const panelToLoad = this.dataset.panel;
            if (panelToLoad) {
                loadContent(panelToLoad);
            }
        });
    });

});