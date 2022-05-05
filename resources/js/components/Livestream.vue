<template>
  <div class="container">
    <h1 class="text-center">Live Stream</h1>
    <div class="row">
      <div class="col-sm">
        <LivestreamVideo :user="user" :others="others" :pusherKey="pusherKey" :pusherCluster="pusherCluster"/>
      </div>
      <div class="col-sm">
        <LivestreamChat :group="group"/>
      </div>
    </div>
  </div>
</template>
<script>

import LivestreamVideo from './LivestreamVideo.vue';
import LivestreamChat from './LivestreamChat.vue';

export default {
  components: {
      LivestreamVideo,
      LivestreamChat
  },
  props: ['user', 'others', 'pusherKey', 'pusherCluster', 'group'],
};
</script>