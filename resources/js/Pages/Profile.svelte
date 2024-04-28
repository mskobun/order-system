<script>
    import { page, router } from "@inertiajs/svelte";
    import Layout from "../Components/Layout.svelte";
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
    import Section from "../Components/Checkout/Section.svelte";

    export let user = {
        name: null,
        email: null,
        phone: null,
        address: null,
    };

    export let updated = false;

    function handleSubmit() {
        router.post("/update_profile", user);
    }
</script>

<Layout
    displayCartButton={false}
>
    <div class="bg-zinc-50 h-screen">
        <div class="flex flex-col items-center px-6 py-8 mx-auto lg:py-0">
            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-2xl xl:p-0 dark:bg-gray-800 dark:border-gray-700"
            >
                <div class="flex flex-row w-full">
                    <div class="p-6 space-y-2 md:space-y-2 sm:p-8 flex-grow">
                        <Heading
                            tag="h1"
                            class="mb-4"
                            customSize="text-xl font-bold md:text-2xl"
                        >
                            My profile
                        </Heading>
                        <form class="space-y-2 md:space-y-3" on:submit|preventDefault={handleSubmit}>
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
                            {#if updated} 
                            <Helper class="m-1 text-sm" color="red">
                                Updated!
                            </Helper>
                            {/if}
                        </form>
                    </div>

                    <div class=" w-1/6"></div>
                </div>
            </div>
        </div>
    </div>
</Layout>
