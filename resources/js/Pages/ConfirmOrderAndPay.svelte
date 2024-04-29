<script>
    import { page, router } from "@inertiajs/svelte";
    import Layout from "../Components/Layout.svelte";
    import {
        Label,
        Alert,
        Card,
        Button,
        ButtonGroup,
        Helper,
    } from "flowbite-svelte";
    import Field from "../Components/Field.svelte";
    import Section from "../Components/Section.svelte";
    import OrderItem from "../Components/OrderItem.svelte";

    import {
        StoreSolid,
        MapPinAltSolid,
        CreditCardSolid,
    } from "flowbite-svelte-icons";
    import OrderSummary from "../Components/OrderSummary.svelte";

    export let errors;
    export let orderDetails = {
        items: null,
        tax: null,
        delivery: null,
        price_reduction: null, 
        discount: null,
        subtotal: null,
        total: null,
    }
    export let detailsUpdated = false;

    let orderParams = {
        name: $page.props.user.name || "",
        phone: $page.props.user.phone || "",
        address: $page.props.user.address || "",
        dineIn: false,
        payment: {
            type: "card",
            number: null,
            expiryMonth: null,
            expiryYear: null,
            cvv: null,
        },
    };

    const updateProfile = () => {
        let details = {
            name: orderParams.name,
            email: $page.props.user.email,
            phone: orderParams.phone,
            address: orderParams.address,
        };

        router.post("/update_profile", details);
    };

    const submitOrder = () => {
        let sendParams = orderParams;
        if (sendParams.dineIn) {
            delete sendParams.address;
        }
        router.post("/order/submit", sendParams);
    };
</script>

<Layout displayCartButton>
    <div class="bg-zinc-50 flex-1">
        <div class="max-w-2xl m-auto bg-white">
            <div
                class="p-6 w-full my-2 bg-white rounded-lg shadow dark:border dark:bg-gray-800 dark:border-gray-700"
            >
                <Section>
                    <h1 class="text-2xl font-bold mb-3">Customer Details</h1>
                    <Field
                        id="name-input"
                        label="Name"
                        error={errors["name"]}
                        bind:value={orderParams.name}
                    />
                    <Field
                        id="phone-input"
                        label="Phone"
                        error={errors["phone"]}
                        bind:value={orderParams.phone}
                    />

                    <ButtonGroup class="mt-2">
                        <Button
                            pill
                            color={orderParams.dineIn
                                ? "primary"
                                : "alternative"}
                            on:click={() => (orderParams.dineIn = true)}
                        >
                            <StoreSolid size="md" />
                            Dine In</Button
                        >
                        <Button
                            pill
                            color={!orderParams.dineIn
                                ? "primary"
                                : "alternative"}
                            on:click={() => (orderParams.dineIn = false)}
                        >
                            <MapPinAltSolid size="md" />
                            Delivery</Button
                        >
                    </ButtonGroup>
                    {#if !orderParams.dineIn}
                        <Field
                            id="address-input"
                            label="Address"
                            error={errors["address"]}
                            bind:value={orderParams.address}
                        />
                    {/if}
                    <div class="mt-3">
                        <Button on:click={() => updateProfile()}>
                            Save Details to Account
                        </Button>
                        {#if detailsUpdated}
                            <Helper class="m-1 text-sm" color="red">
                                Updated!
                            </Helper>
                        {/if}
                    </div>
                </Section>
                <Section>
                    <h1 class="text-2xl font-bold mt-7">Order Summary</h1>
                    {#if errors["items"]}
                        <Alert color="red">{errors["items"]}</Alert>
                    {/if}
                    <div class="divide-y">
                        <OrderSummary {orderDetails}></OrderSummary>
                    </div>
                </Section>
                <Section>
                    <h1 class="text-2xl font-bold mt-4">Payment Details</h1>
                    <Card padding="md">
                        <Field
                            id="card-number-input"
                            label="Card Number"
                            type="number"
                            error={errors["payment.number"]}
                            errorText={"Card number is invalid"}
                            bind:value={orderParams.payment.number}
                        />

                        <Label>Expiry Date</Label>
                        <div class="flex items-begin">
                            <div class="w-32 ml-0 mr-auto">
                                <div class="flex justify-between">
                                    <div class="w-14">
                                        <Field
                                            id="card-number-input"
                                            label="Month"
                                            type="number"
                                            error={errors[
                                                "payment.expiryMonth"
                                            ]}
                                            errorText={"Month is invalid"}
                                            bind:value={orderParams.payment
                                                .expiryMonth}
                                        />
                                    </div>
                                    <div class="w-14">
                                        <Field
                                            id="card-number-input"
                                            label="Year"
                                            type="number"
                                            error={errors["payment.expiryYear"]}
                                            errorText={"Year is invalid"}
                                            bind:value={orderParams.payment
                                                .expiryYear}
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="w-20 mr-0 ml-auto">
                                <Field
                                    id="card-cvv-input"
                                    label="CVV"
                                    type="number"
                                    error={errors["payment.cvv"]}
                                    errorText={"CVV is invalid"}
                                    bind:value={orderParams.payment.cvv}
                                />
                            </div>
                        </div>
                    </Card>
                </Section>
                <div class="m-2">
                    <Button
                        on:click={() => submitOrder()}
                        class="w-full font-bold text-lg mt-8"
                    >
                        <span> Place Order </span>
                        <CreditCardSolid class="ml-0.5 mt-0.5" />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</Layout>
