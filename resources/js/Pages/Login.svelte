<script>
    import { router } from "@inertiajs/svelte";

    export let values = {
        email: null,
        password: null,
        remember: false,
    };

    function handleSubmit() {
        router.post("/login_endpoint", values);
    }
</script>

<form on:submit|preventDefault={handleSubmit}>
    <h>Please login before accessing this page</h>
    <br>
    <label for="email">Email:</label>
    <input id="email" bind:value={values.email} />

    <label for="password">Password:</label>
    <input id="password" bind:value={values.password} />

    <label for="remember">Remember Me:</label>
    <input type ="checkbox" id="remember" bind:checked={values.remember} />

    <button type="submit">Submit</button>
</form>
