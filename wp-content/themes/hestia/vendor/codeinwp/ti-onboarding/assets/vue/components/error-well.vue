<template>
	<div class="error-well">
		<div class="message">
			<p v-html="strings.error_report"></p>
		</div>
		<div class="buttons">
			<form method="get" :action="logUrl">
				<button class="button button-primary" type="submit">
					<i class="dashicons dashicons-download"></i>
					{{strings.download_error_log}}
				</button>
			</form>
		</div>
	</div>
</template>

<script>
  export default {
    name: 'error-well',
    data () {
      return {
        collapsed: true
      };
    },
    computed: {
      errorMessage () {
        return this.$store.state.errorToast;
      },
      strings () {
        return this.$store.state.strings;
      },
      logUrl () {
        return themeisleSitesLibApi.logUrl;
      }
    },
  };
</script>
