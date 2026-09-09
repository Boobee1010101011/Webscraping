<?php

it('registers the scrape report at the root route', function () {
    expect(route('scrape-report'))->toBe(url('/'));
});
