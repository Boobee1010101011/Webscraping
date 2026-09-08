<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Competitor Intelligence Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Filters -->
                    <form method="GET" action="{{ route('scrapes.index') }}" class="mb-6 flex flex-wrap gap-4">
                        <select name="threat_level" onchange="this.form.submit()"
                            class="rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm">
                            <option value="">All Threat Levels</option>
                            <option value="High" {{ request('threat_level') == 'High' ? 'selected' : '' }}>High
                            </option>
                            <option value="Medium" {{ request('threat_level') == 'Medium' ? 'selected' : '' }}>Medium
                            </option>
                            <option value="Low" {{ request('threat_level') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="None" {{ request('threat_level') == 'None' ? 'selected' : '' }}>None
                            </option>
                        </select>

                        <input type="text" name="competitor" placeholder="Search competitor..."
                            value="{{ request('competitor') }}"
                            class="rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-indigo-500">

                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium">
                            Filter
                        </button>

                        @if (request('threat_level') || request('competitor'))
                            <a href="{{ route('scrapes.index') }}"
                                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white self-center">
                                Clear filters
                            </a>
                        @endif
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
                            <thead
                                class="bg-gray-50 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-3 w-32">Competitor</th>
                                    <th class="px-4 py-3 w-24">Threat</th>
                                    <th class="px-4 py-3 w-1/4">Summary</th>
                                    <th class="px-4 py-3 w-1/4">AI Counter Strategy</th>
                                    <th class="px-4 py-3 w-40">Posted At</th>
                                    <th class="px-4 py-3 w-20">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($scrapes as $scrape)
                                    <tr class="align-top hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-4 font-semibold text-gray-100 dark:text-white">
                                            {{ $scrape->competitor_name }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <span @class([
                                                'px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap',
                                                'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' =>
                                                    $scrape->threat_level === 'High',
                                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300' =>
                                                    $scrape->threat_level === 'Medium',
                                                'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' => !in_array(
                                                    $scrape->threat_level,
                                                    ['High', 'Medium']),
                                            ])>
                                                {{ $scrape->threat_level }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-gray-700 dark:text-gray-200">
                                            <p class="line-clamp-3" title="{{ $scrape->english_summary }}">
                                                {{ $scrape->english_summary }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-4 text-gray-700 dark:text-gray-200">
                                            <p class="line-clamp-3" title="{{ $scrape->ai_counter_strategy_draft }}">
                                                {{ $scrape->ai_counter_strategy_draft }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                            {{ $scrape->timestamp ? $scrape->timestamp->timezone('Asia/Phnom_Penh')->format('M d, Y - h:i A') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @if ($scrape->source_url)
                                                <a href="{{ $scrape->source_url }}" target="_blank" rel="noopener"
                                                    class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    View Post
                                                </a>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex justify-center items-center">
                                                No intelligence reports found.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $scrapes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
