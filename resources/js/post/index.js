new Vue({
	el: '#postApp',

	methods: {
		async deletePost(e) {
			console.log({ url: e.target.href });
			if (confirm('Are you sure that you want to delete this post?')) {
				try {
					const deletedPost = await axios.delete(e.target.href);
					window.location.reload();
				} catch (error) {
					console.error('Could not not delete the post:', error);
				}
			}
		},
	},
});
