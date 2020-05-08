@extends('layouts.app')

@section('heading')
	<div class="ui text container">
		<h1>Ваша переписка со службой поддержки</h1>
	</div>
@endsection

@section('content') 
	<div class="ui text container">
		<br>
		<a href="/support/create" class="ui right floated green button"><i class="edit icon"></i>Написать в поддержку</a>
		<table class="ui very basic unstackable table">
			<tbody>
				@forelse($dialogs as $dialog)
					<tr class="top aligned">
						<td width="5%">
							@if($dialog->unreadMessagesCount())
								<div class="ui tiny red circular label">{{ $dialog->unreadMessagesCount() }}</div>
							@else
								@if($dialog->status == 'closed')
									<i class="check green icon"></i>
								@endif
							@endif
						</td>
						<td><a href="/support/{{ $dialog->id }}#read">{{ $dialog->title }}</a></td>
						<td class="right aligned">{{ $dialog->messages_count }} <i class="mail outline icon"></i></td>
					</tr>
				@empty
					<tr><td>
						<div class="ui info message">
							Вы еще не переписывались со службой поддержки
						</div>
					</td></tr>
				@endforelse
				<tr><td colspan="3">{{ $dialogs->links('vendor.pagination.default') }}</td></tr>
			</tbody>
		</table>
	</div>
@endsection

@push('breadcrumb')
    <i class="right angle icon divider"></i>
    <div class="section"><a href="/home">Главная</a></div>
    <i class="right angle icon divider"></i>
    <div class="active section">Поддержка</div>
@endpush
