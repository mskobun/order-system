<script>
    import OrderItem from "./OrderItem.svelte";

    export let orderDetails = {
        items: null,
        subtotal: null,
        delivery_fee: null,
        tax: null, 
        tax_amount: null,
        discount: null,
        discount_amount: null,
        price_reduction: null,
        total: null,
    }
</script>

{#each orderDetails.items as item}
    <OrderItem
        name={item.name}
        amount={item.amount}
        price={item.price}
    />
{/each}

<div class="flex bg-white items-start gap-1">
    <h2 class="p-1 pb-0 pl-0 font-bold">Subtotal:</h2>
    <h2 class="p-1 pb-0 mr-0 ml-auto">
        {orderDetails.subtotal.toFixed(2)}
    </h2>
</div>
<div class="flex bg-white items-start gap-1">
    <h2 class="p-1 pb-0 pl-0 font-bold">Delivery:</h2>
    <h2 class="p-1 pb-0 mr-0 ml-auto">
        {orderDetails.delivery_fee.toFixed(2)}
    </h2>
</div>
<div class="flex bg-white items-start gap-1">
    <h2 class="p-1 pb-0 pl-0 font-bold">Tax:</h2>
    <h2 class="p-1 pb-0 mr-0 ml-auto">
        {(orderDetails.tax * 100).toFixed(2)}% ({(orderDetails.tax_amount).toFixed(2)}) 
    </h2>
</div>
<div class="flex bg-white items-start gap-1">
    <h2 class="p-1 pb-0 pl-0 font-bold">Discount:</h2>
    <h2 class="p-1 pb-0 mr-0 ml-auto">
        {(orderDetails.discount * 100).toFixed(2)}% ({(-orderDetails.discount_amount).toFixed(2)})
    </h2>
</div>
<div class="flex bg-white items-start gap-1">
    <h2 class="p-1 pb-0 pl-0 font-bold">Price Reduction:</h2>
    <h2 class="p-1 pb-0 mr-0 ml-auto">
        {(-orderDetails.price_reduction).toFixed(2)}
    </h2>
</div>
<div class="flex bg-white items-start gap-1">
    <h2 class="p-1 pb-0 pl-0 font-bold">Total:</h2>
    <h2 class="p-1 pb-0 font-bold mr-0 ml-auto">
        {orderDetails.total.toFixed(2)}
    </h2>
</div>