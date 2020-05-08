@extends('layouts.app')

@section('heading')
	<div class="ui text container">
		<h1>Сообщение в службу поддержки</h1>
	</div>
@endsection

@section('content')	
	<form method="POST" action="/support" class="ui form container error" autocomplete="false">
		{{ csrf_field() }}
		<input type="hidden" name="timeOffset">

        @if($errors->any())
			<div class="ui error message">
				<div class="header"> Исправьте ошибки, чтобы добавить сообщение </div>
				<ul class="list">
				@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
				</ul>
			</div>
        @endif

		<div class="required field {{ $errors->has('title') ? 'error' : '' }}">
			<label>Тема запроса</label>
			<input name="title" type="text" placeholder="Суть Вашего запроса" value="{{ (old('title'))?old('title'):'' }}">
		</div>			
		<div class="required field {{ $errors->has('type') ? 'error' : '' }}">
			<select class="ui fluid dropdown" name="type">
				<option value="question" {{ old('type') == 'question' ? 'selected' : '' }}>Вопрос по работе программы</option>
				<option value="idea" {{ old('type') == 'idea' ? 'selected' : '' }}>Идея по улучшению программы</option>
				<option value="error" {{ old('type') == 'error' ? 'selected' : '' }}>Ошибка в работе программы</option>
				<option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Другой запрос</option>
			</select>
		</div>			
		<div class="required field {{ $errors->has('message') ? 'error' : '' }}">
			<label>Сообщение</label>
			<textarea name="message">{{ (old('message'))?old('message'):'' }}</textarea>
		</div>			

		<div class="ui basic clearing segment">
			<button class="ui massive green submit right floated button" type="submit" >Отправить</button>
		</div>
	</form>
@endsection

@push('breadcrumb')
    <i class="right angle icon divider"></i>
    <div class="section"><a href="/home">Главная</a></div>
    <i class="right angle icon divider"></i>
    <div class="section"><a href="/support">Поддержка</a></div>
    <i class="right angle icon divider"></i>
    <div class="active section">Новое сообщение</div>
@endpush

@push('scripts')
	<script>
		$(document).ready(function(){
			$('.ui.checkbox').checkbox();
			$('.ui.dropdown').dropdown();
			var timezoneOffsetMinutes = new Date().getTimezoneOffset();
			timezoneOffsetMinutes = timezoneOffsetMinutes == 0 ? 0 : -timezoneOffsetMinutes;
			$('input[name=timeOffset]').val(timezoneOffsetMinutes)
		});
	</script>
@endpush
