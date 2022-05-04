<template>
  <div class="container">
    <h1 class="text-center">Live Stream</h1>
    <div class="row">
      <div class="col-sm">
        <div class="video-container" ref="video-container">
          <video class="video-here" ref="video-here" autoplay></video>  
          <button @click="stopBothVideoAndAudio()" v-text="with_video ? 'Stop' : 'Play'"/>
          <button v-if="with_video || with_audio" @click="stopAudioOnly()" v-text="with_audio ? 'Mute' : 'Unmute'"/>
          <button v-if="with_video || with_audio" @click="stopVideoOnly()" v-text="with_video ? 'Hide Camera' : 'Show Camera'"/>
        </div>
      </div>
      <div class="col-sm">
        One of three columns
      </div>
    </div>
  </div>
</template>
<script>
import Pusher from 'pusher-js';
import Peer from 'simple-peer';
export default {
  props: ['user', 'others', 'pusherKey', 'pusherCluster'],
  data() {
    return {
      channel: null,
      stream: null,
      peers: {},
      with_video: true,
      with_audio: true,
    }
  },
  mounted() {
    this.setupVideoChat();
  },
  methods: {
    async setupVideoChat() {
      // To show pusher errors
      // Pusher.logToConsole = true;
      const stream = await navigator.mediaDevices.getUserMedia({ video: this.with_video, audio: this.with_audio });
      const videoHere = this.$refs['video-here'];
      videoHere.srcObject = stream;
      this.stream = stream;
    },
    // stop both mic and camera
    stopBothVideoAndAudio() {
        let has_playing = false;
        this.stream.getTracks().forEach(function(track) {
            if (track.readyState == 'live') {
                has_playing = true;
                track.stop();
            }
        });

        if(has_playing) {
            this.with_video = false;
            this.with_audio = false;
        } else {
            this.with_video = true;
            this.with_audio = true;

            this.setupVideoChat()
        }
    },

    // stop only camera
    stopVideoOnly() {
        let with_video = false;
        this.stream.getTracks().forEach(function(track) {
            if (track.readyState == 'live' && track.kind === 'video') {
                track.stop();
            } else if (track.readyState == 'ended' && track.kind === 'video') {
                with_video = true;
            }
        });

        if(with_video) {
            this.with_video = true
            this.setupVideoChat()
        } else {
            this.with_video = false
        }
    },

    // stop only mic
    stopAudioOnly() {
        let with_audio = false;
        this.stream.getTracks().forEach(function(track) {
            if (track.readyState == 'live' && track.kind === 'audio') {
                track.stop();
            } else if (track.readyState == 'ended' && track.kind === 'audio') {
                with_audio = true;
            }
        });

        if(with_audio) {
            this.with_audio = true
            this.setupVideoChat()
        } else {
            this.with_audio = false
        }
    }
  }
};
</script>
<style scoped>
    .video-container {
    width: 500px;
    height: 380px;
    margin: 8px auto;
    border: 3px solid #000;
    position: relative;
    /* box-shadow: 1px 1px 1px #9e9e9e; */
    }
    /* .video-here {
    width: 130px;
    position: absolute;
    left: 10px;
    bottom: 16px;
    border: 1px solid green;
    border-radius: 2px;
    z-index: 2;
    } */
    .video-here {
    width: 100%;
    height: 100%;
    }
    .text-right {
    text-align: right;
    }
</style>