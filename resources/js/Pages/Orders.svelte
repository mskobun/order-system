<script>
    import Layout from "../Components/Layout.svelte";
    import OrderItem from "../Components/OrderItem.svelte";

    export let orders;

    const orderTypes = {
        DELIVERY: "Delivery",
        DINE_IN: "Dine in",
    };

    const statusTypes = {
        PAYMENT_PENDING: "Payment pending",
        ACCEPTED: "Accepted",
        COOKING: "In the kitchen",
        CANCELLED: "Cancelled",
        COMPLETED: "Completed",
        DELIVERY_WAITING_FOR_PICKUP:
            "Waiting for the driver to pick up the order",
        DELIVERY_ON_THE_ROAD: "On the road",
    };
</script>

<Layout>
    <div class="bg-zinc-50 flex-1">
        <div class="m-auto max-w-2xl rounded-lg">
            <h1 class="text-3xl font-bold p-2">My Orders</h1>
            {#each orders as order}
                <div
                    class="bg-white w-full my-2 p-4 rounded-lg shadow dark:border dark:bg-gray-800 dark:border-gray-700"
                >
                    <h2 class="text-2xl font-bold">Order #{order.id}</h2>
                    <div>
                        <b>Name:</b>
                        {order.name}
                    </div>

                    <div>
                        <b>Type:</b>
                        {orderTypes[order.type]}
                    </div>
                    {#if order.type == "DELIVERY"}
                        <div>
                            <b>Address:</b>
                            {order.address}
                        </div>
                    {/if}
                    <div>
                        <b>Date:</b>
                        {order.statuses[0].created_at}
                    </div>

                    <div>
                        <b>Latest Status:</b>
                        {statusTypes[order.statuses[0].status]}
                    </div>

                    {#each order.items as { name, amount, price }}
                        <OrderItem {name} {amount} {price} />
                    {/each}
                    <div class="flex bg-white items-start gap-1">
                        <h2 class="p-1 pb-0 pl-0 font-bold">Subtotal:</h2>
                        <h2 class="p-1 pb-0 mr-0 ml-auto">
                            {order.subtotal.toFixed(2)}
                        </h2>
                    </div>
                    <div class="flex bg-white items-start gap-1">
                        <h2 class="p-1 pb-0 pl-0 font-bold">Tax:</h2>
                        <h2 class="p-1 pb-0 mr-0 ml-auto">
                            {(order.tax * 100).toFixed(2)}%
                        </h2>
                    </div>
                    <div class="flex bg-white items-start gap-1">
                        <h2 class="p-1 pb-0 pl-0 font-bold">Discount:</h2>
                        <h2 class="p-1 pb-0 mr-0 ml-auto">
                            {(order.discount * 100).toFixed(2)}%
                        </h2>
                    </div>
                    <div class="flex bg-white items-start gap-1">
                        <h2 class="p-1 pb-0 pl-0 font-bold">Price Reduction:</h2>
                        <h2 class="p-1 pb-0 mr-0 ml-auto">
                            {(-order.price_reduction).toFixed(2)}
                        </h2>
                    </div>
                    <div class="flex bg-white items-start gap-1">
                        <h2 class="p-1 pb-0 pl-0 font-bold">Delivery:</h2>
                        <h2 class="p-1 pb-0 mr-0 ml-auto">
                            {order.delivery_fee.toFixed(2)}
                        </h2>
                    </div>
                    <div class="flex bg-white items-start gap-1">
                        <h2 class="p-1 pb-0 pl-0 font-bold">Total:</h2>
                        <h2 class="p-1 pb-0 font-bold mr-0 ml-auto">
                            {order.total.toFixed(2)}
                        </h2>
                    </div>
                </div>
            {/each}
        </div>
    </div>
</Layout>
