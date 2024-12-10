<template>
    <button type="submit" class="forms-buttons-action"><slot></slot></button>
</template>

<script>

export default {
    name: "DefaultButton",
}

</script>

<style scoped>

.forms-buttons-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 3rem;

    background-color: var(--primary-color);
    border-radius: var(--border-radius);
    padding: 0.75rem 1.3rem;
    font-size: 0.875rem;
    font-weight: 400;
    /* text-transform: uppercase; */
    transition: background-color var(--transition-speed), transform var(--transition-speed);
    box-shadow: var(--box-shadow);
    color:  var(--text-color-light);
    letter-spacing: 0.1rem;
}

.forms-buttons-action:hover {
    background-color: var(--primary-color-hover);
    transform: scale(1.05);
    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.15);
    cursor: pointer;
}
</style>
