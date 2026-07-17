<?php

function sortByPriority(array $tickets): array
{
    usort($tickets, fn ($a, $b) => $a['priority'] <=> $b['priority']);

    return $tickets;
}
