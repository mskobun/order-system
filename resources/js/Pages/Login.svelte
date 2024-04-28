<script>
    import { inertia, Link, router } from "@inertiajs/svelte";
    import {
        Input,
        Label,
        Helper,
        Button,
        Checkbox,
        Heading,
        A,
    } from "flowbite-svelte";
    import Field from "../Components/Checkout/Field.svelte";

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
    };

    function handleSubmit() {
        router.post("/login_endpoint", values);
    }
</script>

<section class="bg-gray-50 dark:bg-gray-900">
    <div
        class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0"
    >
        <!-- <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
            <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
            Flowbite    
        </a> -->
        <div
            class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700"
        >
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <Heading
                    tag="h1"
                    class="mb-4"
                    customSize="text-xl font-bold md:text-2xl"
                >
                    Sign in to your account
                </Heading>
                <form
                    class="space-y-4 md:space-y-6"
                    on:submit|preventDefault={handleSubmit}
                >
                    <Field
                        id="email"
                        label="Your email"
                        placeholder="example@test.com"
                        bind:value={values.email}
                        error={errors.emailError}
                        errorText={"The entered email doesn't exist in our database."}
                        type="email"
                        required={true}
                    >
                        <Link href="/signup" class="font-medium text-primary-600 dark:text-primary-500">Sign up?</Link>
                    </Field>
                    <Field
                        id="password"
                        label="Password"
                        placeholder="••••••••"
                        bind:value={values.password}
                        error={errors.passwordError}
                        errorText={"Wrong password entered"}
                        type="password"
                        required={true}
                    ></Field>
                    <div class="flex items-center justify-between">
                        <Checkbox
                            bind:checked={values.remember}
                            id="remember"
                            class="space-x-1 rtl:space-x-reverse text-gray-500 dark:text-gray-300"
                            >Remember me</Checkbox
                        >
                        <!-- yet to be implemented -->
                        <!-- <A href="#" class="text-sm font-medium">Forgot password?</A> -->
                    </div>
                    <div class="flex items-center justify-between">
                        <Button type="submit">Sign in</Button>
                    </div>
                    <Helper class="text-sm font-light">
                        Don’t have an account yet? <Link
                            href="/signup"
                            class="font-medium text-primary-600 dark:text-primary-500">Sign up</Link
                        >
                    </Helper>
                </form>
            </div>
        </div>
    </div>
</section>
