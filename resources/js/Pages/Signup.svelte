<script>
    import { inertia, Link, router } from "@inertiajs/svelte"
    import { Input, Label, Helper, Button, Checkbox, Heading, A } from 'flowbite-svelte'

    export let values = {
        name: null,
        email: null,
        password: null,
        password_confirmation: null,
    }

    // the errors here match the names of the value fields
    // they are
    // email, password
    // check for presence to know if they are triggered
    export let errors = {
    }

    function handleSubmit() {
        router.post("/signup_endpoint", values);
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
                    Create an account
                </Heading>
                <form class="space-y-4 md:space-y-6" on:submit|preventDefault={handleSubmit}>
                    <div>
                        <Label for="name" class="mb-2">Your name</Label>
                        <Input bind:value={values.name} type="text" id="name" placeholder="John Doe" required />
                    </div>
                    <div>
                        {#if !('email' in errors)}
                            <Label for="email" class="mb-2">Your email</Label>
                            <Input bind:value={values.email} type="email" id="email" placeholder="example@test.com" required />
                        {:else}
                            <Label for="email-error" class="mb-2" color="red">Your email</Label>
                            <Input bind:value={values.email} color="red" type="email" id="email-error" placeholder="example@test.com" required />
                            <Helper class="text-sm font-light" color="red">
                                {errors.email} <A href="/login" class="font-medium">Login?</A>
                            </Helper>
                        {/if}
                    </div>
                    <div>
                        {#if !('password' in errors && errors.password !== "The password field confirmation does not match.")}
                            <Label for="password" class="mb-2">Password</Label>
                            <Input bind:value={values.password} type="password" id="password" placeholder="••••••••" required />
                        {:else}
                            <Label for="password-error" class="mb-2" color="red">Password</Label>
                            <Input bind:value={values.password} color="red" type="password" id="password-error" placeholder="••••••••" required />
                            <Helper class="text-sm font-light" color="red">
                                {#if errors.password == "The password field format is invalid."}
                                    Your password must contain at least 3 of the following categories:
                                    <br>
                                    - Uppercase characters
                                    <br>
                                    - Lowercase characters
                                    <br>
                                    - Digits
                                    <br>
                                    - Special characters
                                    <br>
                                    - Unicode characters
                                {:else}
                                    {errors.password}
                                {/if}
                            </Helper>
                        {/if}
                    </div>
                    <div>
                        {#if !(errors.password == "The password field confirmation does not match.")}
                            <Label for="confirm-password" class="mb-2">Confirm Password</Label>
                            <Input bind:value={values.password_confirmation} type="password" id="confirm-password" placeholder="••••••••" required />
                        {:else}
                            <Label for="confirm-password-error" class="mb-2" color="red">Confirm Password</Label>
                            <Input bind:value={values.password_confirmation} color="red" type="password" id="confirm-password-error" placeholder="••••••••" required />
                            <Helper class="text-sm font-light" color="red">
                                Passwords don't match!
                            </Helper>
                        {/if}
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <Checkbox bind:checked={values.accept_terms} id="terms" class="space-x-1 rtl:space-x-reverse text-gray-500 dark:text-gray-300" required>
                                I accept the <A href="/terms_and_conditions" class="font-medium">Terms and Conditions</A>
                            </Checkbox>
                        </div>
                    </div>
                    <Button type="submit" class="w-full">Create an account</Button>
                    <Helper class="text-sm font-light">
                        Already have an account? <A href="/login" class="font-medium">Login</A>
                    </Helper>
                </form>
            </div>
        </div>
    </div>
</section>