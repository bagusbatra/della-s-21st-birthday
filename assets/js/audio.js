// Web Audio Synthesizer for Romantic Ambient & Piano Background Music

class RomanticSynthesizer {
  constructor() {
    this.ctx = null;
    this.isPlaying = false;
    this.timerId = null;
    this.currentTrackIndex = 0;
    this.masterGain = null;
    this.volume = 0.4;
    this.listeners = [];

    this.tracks = [
      {
        id: 'harmoni-cinta',
        title: 'Harmoni Kasih (Romantic Piano)',
        mood: 'Menyentuh & Lembut',
        bpm: 64,
      },
      {
        id: 'senja-della',
        title: 'Melodi Senja untuk Della',
        mood: 'Hangat & Syahdu',
        bpm: 72,
      },
      {
        id: 'bunga-abadi',
        title: 'Kelopak Kenangan (Acoustic Chimes)',
        mood: 'Menenangkan & Romantis',
        bpm: 58,
      }
    ];
  }

  initContext() {
    if (!this.ctx) {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      this.ctx = new AudioCtx();
      this.masterGain = this.ctx.createGain();
      this.masterGain.gain.setValueAtTime(this.volume, this.ctx.currentTime);
      this.masterGain.connect(this.ctx.destination);
    }
    if (this.ctx.state === 'suspended') {
      this.ctx.resume();
    }
  }

  subscribe(listener) {
    this.listeners.push(listener);
    return () => {
      this.listeners = this.listeners.filter(l => l !== listener);
    };
  }

  notify() {
    this.listeners.forEach(l => l(this.isPlaying));
  }

  getIsPlaying() {
    return this.isPlaying;
  }

  getVolume() {
    return this.volume;
  }

  setVolume(val) {
    this.volume = Math.max(0, Math.min(1, val));
    if (this.masterGain && this.ctx) {
      this.masterGain.gain.setTargetAtTime(this.volume, this.ctx.currentTime, 0.05);
    }
  }

  getCurrentTrack() {
    return this.tracks[this.currentTrackIndex];
  }

  setTrack(index) {
    this.currentTrackIndex = (index + this.tracks.length) % this.tracks.length;
    if (this.isPlaying) {
      this.stop();
      this.play();
    }
  }

  toggle() {
    if (this.isPlaying) {
      this.stop();
    } else {
      this.play();
    }
  }

  play() {
    this.initContext();
    if (!this.ctx || !this.masterGain) return;
    this.isPlaying = true;
    this.notify();

    let step = 0;

    const chordProgressions = [
      [
        [174.61, 220.00, 261.63, 329.63, 440.00],
        [196.00, 246.94, 293.66, 392.00, 493.88],
        [164.81, 196.00, 246.94, 329.63, 392.00],
        [220.00, 261.63, 329.63, 440.00, 523.25],
        [146.83, 174.61, 220.00, 261.63, 349.23],
        [196.00, 246.94, 293.66, 349.23, 440.00],
        [130.81, 164.81, 196.00, 246.94, 329.63],
        [130.81, 196.00, 246.94, 311.13, 392.00],
      ],
      [
        [261.63, 329.63, 392.00, 523.25],
        [246.94, 293.66, 392.00, 493.88],
        [220.00, 261.63, 329.63, 440.00],
        [164.81, 196.00, 246.94, 329.63],
        [174.61, 220.00, 261.63, 349.23],
        [164.81, 261.63, 329.63, 392.00],
        [146.83, 220.00, 261.63, 349.23],
        [196.00, 246.94, 293.66, 392.00],
      ],
      [
        [146.83, 220.00, 261.63, 329.63, 440.00, 523.25],
        [196.00, 246.94, 329.63, 392.00, 440.00],
        [130.81, 196.00, 246.94, 329.63, 392.00, 493.88],
        [220.00, 261.63, 329.63, 392.00, 493.88, 523.25],
      ]
    ];

    const currentProg = chordProgressions[this.currentTrackIndex] || chordProgressions[0];
    const bpm = this.tracks[this.currentTrackIndex]?.bpm || 65;
    const intervalMs = (60 / bpm) * 1000 * 2;

    const playCycle = () => {
      if (!this.isPlaying || !this.ctx || !this.masterGain) return;
      const chord = currentProg[step % currentProg.length];
      
      this.playPianoTone(chord[0] / 2, 0.45, 3.5, 'triangle');

      chord.forEach((freq, idx) => {
        setTimeout(() => {
          if (!this.isPlaying) return;
          this.playPianoTone(freq, 0.28, 2.8, 'sine');
          if (idx % 2 === 0) {
            this.playPianoTone(freq * 2, 0.08, 1.8, 'sine');
          }
        }, idx * 180);
      });

      setTimeout(() => {
        if (!this.isPlaying) return;
        const melodyFreq = chord[(step * 3 + 1) % 4 % chord.length] * 2;
        this.playPianoTone(melodyFreq, 0.2, 2.2, 'sine');
      }, (intervalMs * 0.5));

      step++;
      this.timerId = setTimeout(playCycle, intervalMs);
    };

    playCycle();
  }

  playPianoTone(freq, gainVal, duration, type = 'sine') {
    if (!this.ctx || !this.masterGain) return;

    try {
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const filter = this.ctx.createBiquadFilter();

      osc.type = type;
      osc.frequency.setValueAtTime(freq, this.ctx.currentTime);

      filter.type = 'lowpass';
      filter.frequency.setValueAtTime(1200, this.ctx.currentTime);
      filter.Q.setValueAtTime(2, this.ctx.currentTime);

      const now = this.ctx.currentTime;
      gain.gain.setValueAtTime(0, now);
      gain.gain.linearRampToValueAtTime(gainVal, now + 0.08);
      gain.gain.exponentialRampToValueAtTime(0.0001, now + duration);

      osc.connect(filter);
      filter.connect(gain);
      gain.connect(this.masterGain);

      osc.start(now);
      osc.stop(now + duration + 0.1);
    } catch (e) {
      // safe catch
    }
  }

  playCelebrationChime() {
    this.initContext();
    if (!this.ctx || !this.masterGain) return;

    const notes = [523.25, 659.25, 783.99, 1046.50, 1318.51];
    notes.forEach((freq, i) => {
      setTimeout(() => {
        if (!this.ctx || !this.masterGain) return;
        const now = this.ctx.currentTime;
        const osc = this.ctx.createOscillator();
        const gain = this.ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, now);
        gain.gain.setValueAtTime(0, now);
        gain.gain.linearRampToValueAtTime(0.35, now + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 1.2);
        osc.connect(gain);
        gain.connect(this.masterGain);
        osc.start(now);
        osc.stop(now + 1.3);
      }, i * 90);
    });
  }

  playEnvelopeOpenSound() {
    this.initContext();
    if (!this.ctx || !this.masterGain) return;

    const notes = [440, 554.37, 659.25, 880];
    notes.forEach((freq, i) => {
      setTimeout(() => {
        if (!this.ctx || !this.masterGain) return;
        const now = this.ctx.currentTime;
        const osc = this.ctx.createOscillator();
        const gain = this.ctx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, now);
        gain.gain.setValueAtTime(0, now);
        gain.gain.linearRampToValueAtTime(0.2, now + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
        osc.connect(gain);
        gain.connect(this.masterGain);
        osc.start(now);
        osc.stop(now + 0.7);
      }, i * 70);
    });
  }

  stop() {
    this.isPlaying = false;
    if (this.timerId) {
      clearTimeout(this.timerId);
      this.timerId = null;
    }
    this.notify();
  }
}

export const romanticSynth = new RomanticSynthesizer();
