<template>
  <div>
    <nav>
      <router-link to="/home">Home</router-link>
      <router-link to="/about">About</router-link>
      <router-link :to="{ name: 'profile', params: { userId: 123 } }"
        >Profile</router-link
      >
    </nav>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: "App",
};
</script>

<style>
.router-link-active {
  font-weight: bold;
}
</style>
