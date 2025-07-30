<template>
  <div v-if="links?.length > 1" class="flex justify-end text-end px-8 mt-4 space-x-2 small">
    <button
      v-for="(link, index) in links"
      :key="index"
      v-html="link.label"
      :class="link.active ? 'bg-custom-blue text-white px-4 py-2 rounded' : 'bg-gray-200 text-gray-700 px-4 py-2 rounded'"
      class="hover:bg-blue-600 text-gray-800 cursor-pointer"
      @click.prevent="changePage(link.url?.split('page=')[1])"
    ></button>
  </div>
</template>

<script>
export default {
  props: {
    links: Array,
    fetchData: Function
  },
  methods: {
    changePage(page) {
      if(page !== null){
        this.fetchData(page);
      }
    }
  }
};
</script>
