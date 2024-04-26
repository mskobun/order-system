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

    $: passwordMatchError = errors.password == "The password field confirmation does not match" 
    $: passwordError = 'password' in errors && !passwordMatchError

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
                        <Label for="email" class="mb-2" color={'email' in errors ? "red" : undefined}>Your email</Label>
                        <Input bind:value={values.email} color={'email' in errors ? "red" : undefined} type="email" id="email" placeholder="example@test.com" required />
                        {#if 'email' in errors}
                            <Helper class="text-sm font-light" color="red">
                                {errors.email} <A href="/login" class="font-medium">Login?</A>
                            </Helper>
                        {/if}
                    </div>
                    <div>
                        <Label for="password" class="mb-2" color={passwordError ? "red" : undefined}>
                            Password
                        </Label>
                        <Input bind:value={values.password} color={passwordError ? "red" : undefined} type="password" id="password" placeholder="••••••••" required />
                        {#if passwordError}
                            <Helper class="text-sm font-light" color="red">
                                {#if !passwordMatchError}
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
                        <Label for="confirm-password" class="mb-2" color={passwordMatchError ? "red" : undefined}>Confirm Password</Label>
                        <Input bind:value={values.password_confirmation} color={passwordMatchError ? "red" : undefined} type="password" id="confirm-password" placeholder="••••••••" required />
                        {#if passwordMatchError}
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