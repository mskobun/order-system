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

    export let values = {
        name: null,
        email: null,
        password: null,
        password_confirmation: null,
        accept_terms: null,
    };

    // the errors here match the names of the value fields
    // they are
    // email, password
    // check for presence to know if they are triggered
    export let errors = {};

    function handleSubmit() {
        router.post("/signup_endpoint", values);
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
                    Create an account
                </Heading>
                <form
                    class="space-y-4 md:space-y-6"
                    on:submit|preventDefault={handleSubmit}
                >
                    <div>
                        <Field
                            id="name"
                            label="Your Name"
                            placeholder="John Doe"
                            bind:value={values.name}
                            type="text"
                            required={true}
                        ></Field>
                    </div>
                    <div>
                        <Field
                            id="email"
                            label="Your Email"
                            placeholder="example@test.com"
                            bind:value={values.email}
                            error={errors.email}
                            errorText={errors.email}
                            type="email"
                            required={true}
                        >
                            <Link href="/login" class="font-medium text-primary-600 dark:text-primary-500">Login?</Link>
                        </Field>
                    </div>
                    <div>
                        <Field
                            id="password"
                            label="Password"
                            placeholder="••••••••"
                            bind:value={values.password}
                            error={errors.password ||
                                errors.passwordLengthError}
                            errorText={""}
                            type="password"
                            required={true}
                        >
                            {#if !errors.passwordLengthError}
                                Your password must contain at least 3 of the
                                following categories:
                                <br />
                                - Uppercase characters
                                <br />
                                - Lowercase characters
                                <br />
                                - Digits
                                <br />
                                - Special characters
                                <br />
                                - Unicode characters
                            {:else}
                                {errors.passwordLengthError}
                            {/if}
                        </Field>
                    </div>
                    <div>
                        <Field
                            id="confirm-password"
                            label="Confirm Password"
                            placeholder="••••••••"
                            bind:value={values.password_confirmation}
                            error={errors.passwordMatchError}
                            errorText={errors.passwordMatchError}
                            type="password"
                            required={true}
                        ></Field>
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <Checkbox
                                bind:checked={values.accept_terms}
                                id="terms"
                                class="space-x-1 rtl:space-x-reverse text-gray-500 dark:text-gray-300"
                                required
                            >
                                I accept the <Link
                                    href="/terms_and_conditions"
                                    class="font-medium text-primary-600 dark:text-primary-500">Terms and Conditions</Link
                                >
                            </Checkbox>
                        </div>
                    </div>
                    <Button type="submit" class="w-full"
                        >Create an account</Button
                    >
                    <Helper class="text-sm font-light">
                        Already have an account? <Link
                            href="/login"
                            class="font-medium text-primary-600 dark:text-primary-500">Login</Link
                        >
                    </Helper>
                </form>
            </div>
        </div>
    </div>
</section>
