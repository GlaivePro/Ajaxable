<?php

namespace GlaivePro\Ajaxable\Traits;

trait AjaxableResponses
{
	public function respondAfter(string $action, $result)
	{
		$method = 'respondAfter'.studly_case($action);
		
		if (method_exists($this, $method))
			return $this->$method($result);
			
		return $this->fallbackResponse($action, $result);
	}
	
	public function fallbackResponse(string $action, $result)
	{
		if (in_array($action, ['create', 'retrieve', 'update', 'updateOrCreate']))
			return $this->respondRow($result);

		if (in_array($action, ['addMedia', 'getMedia']))
		{
			$response = [
				'success' => 1,
				'media' => $result,
				'url' => $result->getFullUrl(),
			];
			
			return $response;	
		}

		$response = ['result' => $result];

		if (config('app.debug'))
			$response['warning'] = 'No response defined for '.$action;
		
		return $response;
	}

	protected function respondAfterDelete($result)
	{
		return ['success' => $result];
	}

	protected function respondAfterDeleteMedia($result)
	{
		return ['success' => $result];
	}

	protected function respondAfterList($result)
	{
		$response = ['success' => 1];

		if (request()->collection !== false)
			$response['collection'] = $result;

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

		$response['view'] = $rendered;

		return $response;
	}

	protected function respondRow($result)
	{
		$response = ['success' => $result];

		if (request()->object !== false)
			$response['object'] = $this;

		if (!request()->has('view'))
			return $response;

		if (request()->has('viewname'))
			$view = request()->viewname;
		else
			$view = $this->getRowView();

		abort_unless(view()->exists($view), 500, 'View '.$view.' not found.');

		$response['view'] = view($view, [self::getPlainClassName() => $this])->render();

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