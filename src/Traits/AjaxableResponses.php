<?php

namespace GlaivePro\Ajaxable\Traits;

trait AjaxableResponses
{
	public function respondAfter(string $action, $result)
	{
		$method = 'respondAfter'.\Str::studly($action);

		if (method_exists($this, $method))
			return $this->$method($result);

		return $this->fallbackResponse($action, $result);
	}

	public function fallbackResponse(string $action, $result)
	{
		if (in_array($action, ['create', 'retrieve', 'update', 'updateOrCreate']))
			return $this->respondRow($result, $action);

		if (in_array($action, ['addMedia', 'getMedia']))
		{
			$response = [
				'status' => 'success',
				'data' => [
					'json' => $result,
					'url' => $result->getFullUrl(),
				],
			];

			return $response;
		}

		$response = [
			'status' => 'success',
			'data' => $result,
		];

		if (config('app.debug'))
			$response['warning'] = 'No response defined for '.$action;

		return $response;
	}

	protected function respondAfterDelete($result)
	{
		return [
			'status' => $result ? 'success' : 'error'
		];
	}

	protected function respondAfterDeleteMedia($result)
	{
		return [
			'status' => $result ? 'success' : 'error'
		];
	}

	protected function respondAfterList($result)
	{
		$response = [
			'status' => $result ? 'success' : 'error',
			'data' => [],
		];

		if (request()->collection !== false)
			$response['data']['json'] = $result;

		if (!request()->has('view'))
			return $response;

		if (request()->has('viewname'))
		{
			$view = request()->viewname;
			abort_unless(view()->exists($view), 500, 'View '.$view.' not found.');
			$rendered = view($view, [str_plural(self::getPlainClassName()) => $result])->render();

		}
		else if (request()->has('rowviewname'))
		{
			$view = request()->rowviewname;
			abort_unless(view()->exists($view), 500, 'View '.$view.' not found.');

			$rendered = '';
			foreach ($result as $row)
				$rendered .= view($view, [self::getPlainClassName() => $row])->render();
		}
		else if (view()->exists($this->getListView()))
		{
			$rendered = view($this->getListView(), [str_plural(self::getPlainClassName()) => $result])->render();
		}
		else
		{
			$view = $this->getRowView();

			abort_unless(view()->exists($view), 500, 'Neither view '.$view.' nor '.$this->getListView().' was found.');

			$rendered = '';
			foreach ($result as $row)
				$rendered .= view($view, [self::getPlainClassName() => $row])->render();
		}

		$response['data']['view'] = $rendered;

		return $response;
	}

	protected function respondRow($result, string $action)
	{
		$response = [
			'status' => $result ? 'success' : 'error',
			'data' => [],
		];

		if (request()->object !== false)
			$response['data']['json'] = $this;

		if (!request()->has('view'))
			return response()->json($response, 201);

		if (request()->has('viewname'))
			$view = request()->viewname;
		else
			$view = $this->getRowView();

		abort_unless(view()->exists($view), 500, 'View '.$view.' not found.');

		$response['data']['view'] = view($view, [self::getPlainClassName() => $this])->render();

		return $response;
	}

	protected function getRowView()
	{
		if ($this->rowView)
			return $this->rowView;

		return 'ajaxable.'.self::getPlainClassName();
	}

	protected function getListView()
	{
		if ($this->listView)
			return $this->listView;

		return 'ajaxable.'.str_plural(self::getPlainClassName());
	}
}
