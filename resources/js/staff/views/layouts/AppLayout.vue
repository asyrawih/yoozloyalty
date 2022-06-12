<template>
    <div id="app-container">
        <router-view></router-view>
    </div>
</template>

<script>
    export default {
        name: 'AppLayout',
        watch: {
            $route: {
                handler() {
                    if (this.$auth.check()) {
                        axios.get('staff/auth/abilities').then(response => {
                            this.$ability.update([
                                { subject: 'all', action: response.data.abilities },
                            ]);
                        })
                    }
                },
                immediate: true,
            }
        },
    };
</script>

<style scoped></style>
