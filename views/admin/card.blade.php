        <div class="user-card hover:bg-indigo-100">
            <div class="mr-3 relative w-12 h-12 text-gray-500">
                <svg class="fill-current inline-block w-12 h-12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48"><path d="M6 14H4a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2h12a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v13a1 1 0 0 1-1.7.7L16.58 18H8a2 2 0 0 1-2-2v-2zm0-2V8c0-1.1.9-2 2-2h8V4H4v8h2zm14-4H8v8h9a1 1 0 0 1 .7.3l2.3 2.29V8z"/></svg>
                @if($dialog->unread > 0)
                    <div class="absolute text-sm top-0 right-0 mt-1">
                        <div class="text-red-600">●</div> 
                    </div>
                    <div class="absolute text-gray-700 top-0 left-0 w-12 mt-3 ml-1 text-center">
                        {{ $dialog->unread }}
                    </div>
                @endif
            </div>
            <div class="">
                <a href="/support/dialog/{{ $dialog->id }}#read">{{ $dialog->name }}</a>
                <span class="text-sm">{{ $dialog->email }}</span>
                <div class="text-gray-500 text-sm mt-1">
                    {{ $dialog->support_messages[0]->created_at->addHours(3)->format('d.m.Y H:i') }}
                    @lang('simplesupport::admin.moscowTime')
                </div>
                <div class="text-sm mt-1 hover:no-underline">
                    @if($dialog->message)
                        {!! $dialog->message !!}
                    @else 
                        <a href="/support/dialog/{{ $dialog->id }}#read" class="hover:no-underline">
                            {!! $dialog->support_messages[0]->message !!}
                        </a>
                    @endif
                </div>
            </div>
        </div>
