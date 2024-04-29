<script>
    import { router } from "@inertiajs/svelte";
    import Layout from "../Components/Layout.svelte";
    import { Helper, Button, Heading } from "flowbite-svelte";
    import Field from "../Components/Field.svelte";
    import PasswordBox from "../Components/PasswordBox.svelte";

    export let user = {
        name: null,
        email: null,
        phone: null,
        address: null,
    };

    export let passwords = {};

    export let errors = {};

    export let updatedProfile = false;
    export let updatedPassword = false;

    function submitUser() {
        router.post("/update_profile", user);
    }

    function submitPassword() {
        router.post("/update_password", passwords);
    }
</script>

<Layout>
    <div class="bg-zinc-50 flex-1">
        <div class="flex flex-col items-center mx-auto max-w-2xl">
            <Heading tag="h1" customSize="text-3xl font-bold p-2">
                My Profile
            </Heading>
            <div
                class="w-full my-2 bg-white rounded-lg shadow dark:border dark:bg-gray-800 dark:border-gray-700"
            >
                <div class="flex flex-row w-full">
                    <div class="flex flex-col w-full h-full">
                        <div class="p-6 flex-grow">
                            <Heading
                                tag="h1"
                                class="mb-4"
                                customSize="text-xl font-bold md:text-2xl"
                            >
                                User Details
                            </Heading>
                            <form
                                class="space-y-2 md:space-y-3"
                                on:submit|preventDefault={submitUser}
                            >
                                <Field
                                    id="name"
                                    label="Name"
                                    placeholder="John Doe"
                                    bind:value={user.name}
                                    error={false}
                                    type="text"
                                    required={true}
                                ></Field>
                                <Field
                                    id="email"
                                    label="Email"
                                    placeholder="example@test.com"
                                    bind:value={user.email}
                                    error={false}
                                    type="email"
                                    required={true}
                                ></Field>
                                <Field
                                    id="phone"
                                    label="Phone Number"
                                    placeholder="012-3456789"
                                    bind:value={user.phone}
                                    error={false}
                                    type="tel"
                                    required={true}
                                ></Field>
                                <Field
                                    id="address"
                                    label="Address"
                                    placeholder="House 3, Placeholder Street, Malaysia"
                                    bind:value={user.address}
                                    error={false}
                                    type="text"
                                    required={true}
                                ></Field>
                                <Button type="submit">Update</Button>
                                {#if updatedProfile}
                                    <Helper class="m-1 text-sm" color="red">
                                        Updated!
                                    </Helper>
                                {/if}
                            </form>
                        </div>
                        <div class="pt-0 p-6 flex-grow">
                            <Heading
                                tag="h1"
                                class="mb-4"
                                customSize="text-xl font-bold md:text-2xl"
                            >
                                Password
                            </Heading>
                            <form
                                class="space-y-2 md:space-y-3"
                                on:submit|preventDefault={submitPassword}
                            >
                                <PasswordBox
                                    bind:values={passwords}
                                    {errors}
                                    className={"space-y-2 md:space-y-3"}
                                    showOldPassword
                                    required
                                />
                                <Button type="submit">Update</Button>
                                {#if updatedPassword}
                                    <Helper class="m-1 text-sm" color="red">
                                        Updated!
                                    </Helper>
                                {/if}
                            </form>
                        </div>
                    </div>

                    <div class=" w-1/6"></div>
                </div>
            </div>
        </div>
    </div>
</Layout>
