@props([
    'invitation',
    'action',
])

<div data-test="team-invitation-alert">
    <div class="flex gap-3 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900 dark:border-blue-900/50 dark:bg-blue-950/50 dark:text-blue-100">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-blue-600 dark:text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8h.01M11 12h1v4h1m-1-9a9 9 0 1 1 0 18 9 9 0 0 1 0-18Z" />
        </svg>

        <div>
            {{ __(':action to join the ":team" team.', ['action' => $action, 'team' => $invitation['teamName']]) }}
        </div>
    </div>
</div>
