<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\Calculator;
use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


final class HomePresenter extends Nette\Application\UI\Presenter
{
    const DEFAULT_VALUES = "300ks; 500 Kč\n300ks: 500 Kč\n300, 500\n300 ks, 500Kč\n300 kusů, 500 Kč\n3000ks, 1 500 Kč\n3 000ks, 1500 Kč";

    public function __construct(private Calculator $calculator)
    {
    }

    /**
     * @return void
     */
    public function actionDefault(): void
    {

    }

    /**
     * @return Form
     */
    public function createComponentCalculatorForm(): Form
    {
        $form = new Form;

        $form->addSelect('supplier', 'Dodavatelé', Calculator::SUPPLIERS)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Dodavatel je povinný');

        $form->addTextArea('offer', 'Nabídka')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('rows', '10')
            ->setDefaultValue(self::DEFAULT_VALUES)
            ->setRequired('Nabídka je povinná');

        $form->addProtection();

        $form->addSubmit('send', 'Přepočítat');

        $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     */
    public function calculatorFormSucceeded(Form $form, ArrayHash $values) : void
    {
        $this->template->results = $this->calculator->processData($values);
    }
}
