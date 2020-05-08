@extends('admin.layout')

@section('content')
<h1 class="page-header">
    <a href="/support" class="text-white">@lang('simplesupport::admin.listTitle')</a> 
</h1>
<h2 class="sub-header">
	<svg class="fill-current inline-block w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6 14H4a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2h12a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v13a1 1 0 0 1-1.7.7L16.58 18H8a2 2 0 0 1-2-2v-2zm0-2V8c0-1.1.9-2 2-2h8V4H4v8h2zm14-4H8v8h9a1 1 0 0 1 .7.3l2.3 2.29V8z"/></svg>
	{{ $user->name }}
</h2>

<div class="my-6 mx-2 md:mx-10 flex">
	<div class="w-1/2 text-right pr-2">
		@if(! $messages->onFirstPage())
			<a class="selector" href="{{ $messages->previousPageUrl() }}">
				‹ @lang('simplesupport::admin.later')
			</a>
		@endif
	</div>
	<div class="w-1/2 pl-2">
		@if($messages->hasMorePages())
			<a class="selector" href="{{ $messages->nextPageUrl() }}">
				@lang('simplesupport::admin.earlier') ›
			</a>
		@endif
	</div>
</div>

<div class="mx-2 md:mx-10">
@for($index=count($messages)-1 ; $index>=0 ; $index--)
	<div class="support-message 
		@if($messages[$index]->type == 'userMessage') user @else admin @endif">
		<div class="user-info">
			@if($messages[$index]->type == 'userMessage') {{ $user->name }} @endif
			{{ $messages[$index]->created_at->addHours(3)->format('d.m.Y H:i') }} 
			@lang('simplesupport::admin.moscowTime')
		</div>
		<div class="message"> 
    		{!! $messages[$index]->message !!}
    		@if($messages[$index]->type != 'userMessage')
    			<div class="controls">
    				<a href="/support/message/{{ $messages[$index]->id }}/delete" 
    					onclick="return confirm('@lang('simplesupport::admin.confirmDelete')')">
    					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="inline-block fill-current text-gray-500"><path class="heroicon-ui" d="M8 6V4c0-1.1.9-2 2-2h4a2 2 0 0 1 2 2v2h5a1 1 0 0 1 0 2h-1v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8H3a1 1 0 1 1 0-2h5zM6 8v12h12V8H6zm8-2V4h-4v2h4zm-4 4a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0v-6a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0v-6a1 1 0 0 1 1-1z"/></svg>
    				</a>
    			</div>
    		@endif
    	</div>
    </div>
@endfor
</div>

<div class="my-4 mx-2 md:mx-10 pl-10">
	<form action="/support/dialog/{{ $user->id }}#read" method="POST">
		@csrf
		<div class="flex">
			<div class="w-full">
				<div class="text-sm text-gray-500 text-right">
					@lang('simplesupport::admin.admin')
				</div>
				<textarea class="w-full rounded-b rounded-tl px-4 py-4 border-2 focus:outline-none
					@error('message') border-red-500 @else border-gray-300 @enderror" 
					name="message" rows="3" id="read">{{ old('message') }}</textarea>
			</div>
			<div class="pt-6 ml-1">
				<button class="button circle">
					<svg class="fill-current w-20 h-20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 385.707 385.707" style="enable-background:new 0 0 385.707 385.707;">
						<path d="M382.83,17.991c-2.4-2-5.6-2.4-8.4-1.2l-365.2,160c-6,2.4-9.6,8.4-9.2,15.2c0.4,6.8,4.4,12.4,10.8,14.8l106.8,35.2v96
							c0,8.8,5.6,16.4,14,18.8c8.4,2.8,17.6-0.4,22.8-7.6l44.8-64.8l94.8,81.6c2.8,2.4,6.4,3.6,10.4,3.6c2,0,3.6-0.4,5.2-0.8
							c5.6-2,9.6-6.4,10.4-12l65.6-330.8C386.03,23.191,384.83,19.991,382.83,17.991z M191.23,267.591l-50,72.4c-1.6,2.4-3.6,2-4.8,1.6
							c-0.8,0-2.8-1.2-2.8-3.6v-101.6c0-3.6-2-6.4-5.6-7.6l-112.4-37.6l324.8-142l-160,131.6c-3.6,2.8-4,8-1.2,11.2c1.6,2,4,2.8,6,2.8
							c1.6,0,3.6-0.4,5.2-2l138.8-114L191.23,267.591z M304.43,353.591l-96-82.4l153.6-209.6L304.43,353.591z"/>
						<path d="M158.83,198.391l-12.8,10.4c-3.6,2.8-4,8-1.2,11.2c1.6,2,4,2.8,6.4,2.8c1.6,0,3.6-0.4,5.2-1.6l12.8-10.4
							c3.6-2.8,4-8,1.2-11.2C167.63,196.391,162.43,195.991,158.83,198.391z"/>
					</svg>
				</button>
			</div>
		</div>
	</form>
</div>

@endsection


