<template>
    <img :src="logo_b" alt="Logo" class="logo dark-mode d-none" width="300" />
    <img :src="logo" alt="Logo" class="logo  light-mode" width="300" />
</template>
<script>
import logo from '@/../assets/erisn_logo_white.png';
import logo_b from '@/../assets/erisn_logo_black.png';

export default {
  data() {
    return {
      logo,
      logo_b
    };
  }
};
</script>
