<script>
    import { Link, router } from "@inertiajs/svelte";
    import { Helper, Button, Checkbox, Heading } from "flowbite-svelte";
    import Field from "../Components/Field.svelte";
    import PasswordBox from "../Components/PasswordBox.svelte";

    export let values = {
        name: null,
        email: null,
        accept_terms: null,
    };

    export let passwords = {};

    // the code just checks whether a certain key (error) is present here
    export let errors = {};

    function handleSubmit() {
        let data = { ...values, ...passwords };
        router.post("/signup_endpoint", data);
    }
</script>

<div class="flex flex-col">
    <section class="bg-zinc-50 flex-1 dark:bg-gray-900">
        <div
            class="flex flex-col items-center justify-center px-6 py-8 mx-auto h-screen lg:py-0"
        >
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
                        <Field
                            id="name"
                            label="Your Name"
                            placeholder="John Doe"
                            bind:value={values.name}
                            type="text"
                            required={true}
                        ></Field>
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
                            <Link
                                href="/login"
                                class="font-medium text-primary-600 dark:text-primary-500"
                                >Login?</Link
                            >
                        </Field>

                        <PasswordBox
                            bind:values={passwords}
                            {errors}
                            className={"space-y-4 md:space-y-6"}
                            required
                        />
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
                                        class="font-medium text-primary-600 dark:text-primary-500"
                                        >Terms and Conditions</Link
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
                                class="font-medium text-primary-600 dark:text-primary-500"
                                >Login</Link
                            >
                        </Helper>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
