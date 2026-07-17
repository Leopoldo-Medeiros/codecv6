## Bug Report

`excerpt()` is supposed to cap a preview string at `$maxLength` characters
**total**, appending `...` when it cuts the text short. QA reports that
previews are consistently a few characters longer than the limit they
configured — enough to break a fixed-width card layout.

The test below is already written and currently **fails**. Find the bug
and fix it.
