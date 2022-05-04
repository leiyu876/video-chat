<template>
  <div class="container">
    <h1 class="text-center">Laravel Video Chat</h1>
    <div class="video-container" ref="video-container">
      <video class="video-here" ref="video-here" autoplay></video>  
      <video class="video-there" ref="video-there" autoplay></video>
      <div class="text-right" v-for="(name,userId) in others" :key="userId">
        <button @click="startVideoChat(userId)" v-text="`Talk with ${name}`"/>
      </div>
      <button @click="stopBothVideoAndAudio()" v-text="with_video ? 'Stop' : 'Play'"/>
      <button v-if="with_video || with_audio" @click="stopAudioOnly()" v-text="with_audio ? 'Mute' : 'Unmute'"/>
      <button v-if="with_video || with_audio" @click="stopVideoOnly()" v-text="with_video ? 'Hide Camera' : 'Show Camera'"/>
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
    startVideoChat(userId) {
      this.getPeer(userId, true);
    },
    getPeer(userId, initiator) {
        console.log(this.peers)
      if(this.peers[userId] === undefined) {
        let peer = new Peer({
          initiator,
          stream: this.stream,
          trickle: false
        });
        peer.on('signal', (data) => {
          this.channel.trigger(`client-signal-${userId}`, {
            userId: this.user.id,
            data: data
          });
        })
        .on('stream', (stream) => {
          const videoThere = this.$refs['video-there'];
          videoThere.srcObject = stream;
        })
        .on('close', () => {
          const peer = this.peers[userId];
          if(peer !== undefined) {
            peer.destroy();
          }
          delete this.peers[userId];
        });
        this.peers[userId] = peer;
      } 
      return this.peers[userId];
    },
    async setupVideoChat() {
      // To show pusher errors
      // Pusher.logToConsole = true;
      const stream = await navigator.mediaDevices.getUserMedia({ video: this.with_video, audio: this.with_audio });
      const videoHere = this.$refs['video-here'];
      videoHere.srcObject = stream;
      this.stream = stream;
      const pusher = this.getPusherInstance();
      this.channel = pusher.subscribe('presence-video-chat');
      this.channel.bind(`client-signal-${this.user.id}`, (signal) => 
      {
          console.log(signal)
        const peer = this.getPeer(signal.userId, false);
        peer.signal(signal.data);
      });
    },
    getPusherInstance() {
      return new Pusher(this.pusherKey, {
        authEndpoint: '/auth/video_chat',
        cluster: this.pusherCluster,
        auth: {
          headers: {
            'X-CSRF-Token': document.head.querySelector('meta[name="csrf-token"]').content
          }
        }
      });
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
    box-shadow: 1px 1px 1px #9e9e9e;
    }
    .video-here {
    width: 130px;
    position: absolute;
    left: 10px;
    bottom: 16px;
    border: 1px solid #000;
    border-radius: 2px;
    z-index: 2;
    }
    .video-there {
    width: 100%;
    height: 100%;
    z-index: 1;
    }
    .text-right {
    text-align: right;
    }
</style>