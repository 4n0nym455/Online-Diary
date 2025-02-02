// Define variables for audio recording
let mediaRecorder;
let audioChunks = [];
let audioStream;
let audioContext;
let analyser;
let bufferLength;
let dataArray;
let startButton = document.getElementById("start-recording");
let stopButton = document.getElementById("stop-recording");
let audioPreview = document.getElementById("audio-preview");
let recordedAudioInput = document.getElementById("recorded-audio");

// Function to start the recording
startButton.addEventListener('click', async () => {
    // Request audio stream from user's device
    try {
        audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });

        // Create a new MediaRecorder instance
        mediaRecorder = new MediaRecorder(audioStream);

        // Set up Web Audio API for better audio processing
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        analyser = audioContext.createAnalyser();
        analyser.fftSize = 256;
        bufferLength = analyser.frequencyBinCount;
        dataArray = new Uint8Array(bufferLength);

        let source = audioContext.createMediaStreamSource(audioStream);
        source.connect(analyser);

        // Start recording
        mediaRecorder.start();
        startButton.disabled = true;
        stopButton.disabled = false;

        // Collect audio chunks
        mediaRecorder.ondataavailable = event => {
            audioChunks.push(event.data);
        };

        mediaRecorder.onstop = () => {
            // Create a Blob from the recorded audio chunks
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const audioUrl = URL.createObjectURL(audioBlob);

            // Set audio preview
            audioPreview.src = audioUrl;

            // Encode audio as base64 and save it to the hidden input
            const reader = new FileReader();
            reader.onloadend = function () {
                recordedAudioInput.value = reader.result;
            };
            reader.readAsDataURL(audioBlob);
        };

        // Visualize audio levels (Optional for feedback)
        visualizeAudio();
    } catch (error) {
        console.error('Error accessing audio devices: ', error);
    }
});

// Function to stop the recording
stopButton.addEventListener('click', () => {
    mediaRecorder.stop();
    startButton.disabled = false;
    stopButton.disabled = true;
    audioStream.getTracks().forEach(track => track.stop()); // Stop the audio stream
});

// Function to visualize the audio levels (Optional)
function visualizeAudio() {
    const canvas = document.createElement('canvas');
    document.body.appendChild(canvas);
    const canvasCtx = canvas.getContext('2d');

    function draw() {
        requestAnimationFrame(draw);

        analyser.getByteFrequencyData(dataArray);

        canvasCtx.fillStyle = 'rgb(200, 200, 200)';
        canvasCtx.fillRect(0, 0, canvas.width, canvas.height);

        const barWidth = (canvas.width / bufferLength) * 2.5;
        let x = 0;

        for (let i = 0; i < bufferLength; i++) {
            const barHeight = dataArray[i];
            canvasCtx.fillStyle = 'rgb(' + (barHeight + 100) + ',50,50)';
            canvasCtx.fillRect(x, canvas.height - barHeight / 2, barWidth, barHeight);

            x += barWidth + 1;
        }
    }
    draw();
}
