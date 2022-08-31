<?php

namespace Coderflex\LaravelCsv\Concerns;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\TabularDataReader;

/**
 * Coderflex\LaravelCsv\Concerns\HasCsvProperties
 *
 * @property Reader $readCsv
 * @property \League\Csv\TabularDataReader $csvRecords
 */
trait HasCsvProperties
{
    use InteractsWithCsvFiles;

    /**
     * Read CSV Property
     *
     * @return Reader
     */
    public function getReadCsvProperty(): Reader
    {
        return $this->readCSV($this->file->getRealPath());
    }

    /**
     * Get CSV Records Property
     *
     * @return TabularDataReader
     */
    public function getCsvRecordsProperty(): TabularDataReader
    {
        return Statement::create()->process($this->readCsv);
    }

    /**
     * Handle CSV Information properties from the given file
     *
     * @return array|Illuminate\Support\MessageBag
     */
    public function handleCsvProperties(): array|MessageBag
    {
        try {
            $fileHeaders = $this->readCsv->getHeader();
            $fileRowCount = $this->csvRecords->count();

            return [$fileHeaders, $fileRowCount];
        } catch(\League\Csv\SyntaxError $exception) {
            Log::warning($exception->getMessage());

            return $this->addError(
                'file_error',
                __('Your CSV file has error/errors, or is empty. Please check, and try again')
            );
        }
    }
}
