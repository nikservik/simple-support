@extends('layouts.app')

@section('heading')
	<div class="ui text container">
		<h1>Ваша переписка со службой поддержки</h1>
	</div>
@endsection

@section('content') 
	<div class="ui container">
		<br>
		<div class="ui stackable grid">
			<div class="sixteen wide mobile only column">
				<h3><a href="/support"><i class="reply icon"></i></a> {{ $dialog->title }}</h3>
			</div>
			<div class="six wide computer only seven wide tablet only column">
				<div class="ui secondary fluid vertical menu">
					<div class="active item"><i class="{{ ($dialog->status == 'closed')?'check green':'' }} icon"></i>{{ $dialog->title }}</div>
					@foreach($dialogs as $otherDialog)
						@if($dialog->id != $otherDialog->id)
							<a href="/support/{{ $otherDialog->id }}#read" class="item"><i class="{{ ($otherDialog->status == 'closed')?'check green':'' }} icon"></i>{{ $otherDialog->title }}</a>
						@endif
					@endforeach
				</div>
				<a href="/support"><i class="reply icon"></i>Назад к списку запросов</a>
			</div>
			<div class="ten wide computer nine wide tablet sixteen wide mobile column">
				<div>
					<span class="ui basic blue label">{{ $dialog->humanType }}</span>
					<span class="ui {{ $dialog->status=='open'?'green':'grey' }} label">{{ $dialog->humanStatus }}</span>
				</div>
				@foreach($dialog->showMessagesForUser as $message)
					<div class="ui message {{ $message->type == 'userMessage' ? 'userMessage' : 'positive supportMessage' }}">
						@nl2br($message->message)
						<div class="extra">
							{{  userTime($message->created_at)->format('d.m.Y H:i') }}
						</div>
					</div>
					@if($message->lastRead)
						<a name="read"></a>
						@if(!$loop->last)
							<h4 class="ui horizontal divider header">Непрочитанные сообщения</h4>
						@endif
					@endif
				@endforeach

				<form method="POST" action="/support/{{ $dialog->id }}" class="ui form container error" autocomplete="false">
					{{ csrf_field() }}
					<div class="ui message userMessage">
						<textarea name="message" rows="3">{{ (old('message'))?old('message'):'' }}</textarea>
					</div>
					<button class="ui green submit right floated button">Отправить</button>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('breadcrumb')
    <i class="right angle icon divider"></i>
    <div class="section"><a href="/home">Главная</a></div>
    <i class="right angle icon divider"></i>
    <div class="section"><a href="/support">Поддержка</a></div>
    <i class="right angle icon divider"></i>
    <div class="active section">{{ $dialog->title }}</div>
@endpush
