<!-- An individual order item -->
<script>
    export let name, amount, price;
</script>

<div class="flex bg-white py-2 items-start gap-1">
    <div
        class="w-8 h-8 border-2 border-primary-600 flex justify-center items-center rounded"
    >
        {amount}x
    </div>
    <h2>{name}</h2>

    <h2 class="mr-0 ml-auto">
        {(price * amount).toFixed(2)}
    </h2>
</div>
