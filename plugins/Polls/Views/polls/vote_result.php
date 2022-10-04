<span data-placement='right' data-bs-toggle='tooltip' title='<?php echo $vote_result->poll_voters; ?>'>
    <span data-feather="heart" class="icon-16"></span> <?php echo $vote_result->total_vote; ?>
</span>

<script>
    "use strict";

    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>