<script>
    import Layout from "../Components/Layout.svelte";
    import OrderItem from "../Components/OrderItem.svelte";

    export let orders;
</script>

<Layout>
    <div class="bg-zinc-50 flex-1">
        <div 
            class="m-auto max-w-2xl rounded-lg"
        >
            <h1 class="text-3xl font-bold p-2">My Orders</h1>
            {#each orders as order}
                <div class="bg-white w-full my-2 p-4 rounded-lg shadow dark:border dark:bg-gray-800 dark:border-gray-700">
                    <h2 class="text-2xl font-bold">Order #{order.id}</h2>
                    <div>
                        <b>Name:</b>
                        {order.name}
                    </div>

                    <div>
                        <b>Type:</b>
                        {order.type}
                    </div>
                    {#if order.type=="DELIVERY"}
                    <div>
                        <b>Address:</b>
                        {order.address}
                    </div>
                    {/if}

                    <div>
                        <b>Latest Status:</b>
                        {order.statuses[0].status}
                    </div>

                    {#each order.items as { name, amount, price }}
                        <OrderItem {name} {amount} {price} />
                    {/each}
                    <div class="flex bg-white py-2 items-start gap-1">
                        <h2 class="font-bold">Total:</h2>
                        <h2 class="font-bold mr-0 ml-auto">
                            {order.total.toFixed(2)}
                        </h2>
                    </div>
                </div>
            {/each}
        </div>
    </div>
</Layout>
