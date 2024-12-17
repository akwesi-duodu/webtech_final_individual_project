<script>
// Add click event listeners to seats
document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('click', () => {
        seat.classList.toggle('selected');
    });
});
</script>