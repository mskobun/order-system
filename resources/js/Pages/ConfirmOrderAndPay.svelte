<script>
    import { page, router } from "@inertiajs/svelte";
    import Layout from "../Components/Layout.svelte";
    import { Label, Alert, Card, Button, ButtonGroup } from "flowbite-svelte";
    import Field from "../Components/Checkout/Field.svelte";
    import Section from "../Components/Checkout/Section.svelte";
    import OrderItem from "../Components/OrderItem.svelte";

    import {
        StoreSolid,
        MapPinAltSolid,
        CreditCardSolid,
    } from "flowbite-svelte-icons";

    export let order_items;
    export let errors;
    $: total = order_items.reduce(
        (acc, item) => acc + item.price * item.amount,
        0,
    );

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

<Layout>
    <div class="bg-zinc-50">
        <div class="m-5 max-w-screen-xl m-auto bg-white">
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
                        color={orderParams.dineIn ? "primary" : "alternative"}
                        on:click={() => (orderParams.dineIn = true)}
                    >
                        <StoreSolid size="md" />
                        Dine In</Button
                    >
                    <Button
                        pill
                        color={!orderParams.dineIn ? "primary" : "alternative"}
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
                    <Button
                        on:click={() => updateProfile()}
                    >
                        Save Details to Account
                    </Button>
                </div>
            </Section>
            <Section>
                <h1 class="text-2xl font-bold">Order Summary</h1>
                {#if errors["items"]}
                    <Alert color="red">{errors["items"]}</Alert>
                {/if}
                <div class="divide-y">
                    {#each order_items as item}
                        <OrderItem
                            name={item.name}
                            amount={item.amount}
                            price={item.price}
                        />
                    {/each}

                    <div class="flex bg-white py-2 items-start gap-1">
                        <h2 class="font-bold">Total:</h2>
                        <h2 class="font-bold mr-0 ml-auto">
                            {total.toFixed(2)}
                        </h2>
                    </div>
                </div>
            </Section>
            <Section>
                <h1 class="text-2xl font-bold">Payment Details</h1>
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
                                        error={errors["payment.expiryMonth"]}
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
                    class="w-full font-bold text-lg"
                >
                    <span> Place Order </span>
                    <CreditCardSolid class="ml-0.5 mt-0.5" />
                </Button>
            </div>
        </div>
    </div>
</Layout>
