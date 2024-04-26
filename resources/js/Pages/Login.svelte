<script>
    import { inertia, Link, router } from "@inertiajs/svelte";
    import { Input, Label, Helper, Button, Checkbox, Heading, A } from 'flowbite-svelte';

    // continuation is the url to jump to after login
    export let values = {
        email: null,
        password: null,
        remember: false,
        continuation: null,
    };

    export let errors = {
        emailError: false,
        passwordError: false,
    }

    function handleSubmit() {
        router.post("/login_endpoint", values);
    }
</script>

<section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <!-- <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
            <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
            Flowbite    
        </a> -->
        <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <Heading tag="h1" class="mb-4" customSize="text-xl font-bold md:text-2xl">
                    Sign in to your account
                </Heading>
                <form class="space-y-4 md:space-y-6" on:submit|preventDefault={handleSubmit}>
                    <div>
                        <Label for="email" class="mb-2" color={errors.emailError ? "red" : undefined}>Your email</Label>
                        <Input bind:value={values.email} color={errors.emailError ? "red" : undefined} type="email" id="email" placeholder="example@test.com" required />
                        {#if errors.emailError}
                            <Helper class="text-sm font-light" color="red">
                                The entered email doesn't exist in our database. <A href="/signup" class="font-medium">Sign up?</A>
                            </Helper>
                        {/if}
                    </div>
                    <div>
                        <Label for="password" class="mb-2" color={errors.passwordError ? "red" : undefined}>Password</Label>
                        <Input bind:value={values.password} color={errors.passwordError ? "red" : undefined} type="password" id="password" placeholder="••••••••" required />
                        {#if errors.passwordError}
                            <Helper class="text-sm font-light" color="red">
                                Wrong password entered. 
                            </Helper>
                        {/if}
                    </div>
                    <div class="flex items-center justify-between">
                        <Checkbox bind:checked={values.remember} id="remember" class="space-x-1 rtl:space-x-reverse text-gray-500 dark:text-gray-300">Remember me</Checkbox>
                        <!-- yet to be implemented -->
                        <!-- <A href="#" class="text-sm font-medium">Forgot password?</A> -->
                    </div>
                    <div class="flex items-center justify-between">
                        <Button type="submit">Sign in</Button>
                    </div>
                    <Helper class="text-sm font-light">
                        Don’t have an account yet? <A href="/signup" class="font-medium">Sign up</A>
                    </Helper>
                </form>
            </div>
        </div>
    </div>
</section>