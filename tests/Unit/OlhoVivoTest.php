<?php

namespace RafaelDuarte\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RafaelDuarte\OlhoVivo;

class OlhoVivoTest extends TestCase
{
    public OlhoVivo $api;

    protected function setUp(): void
    {
        parent::setUp();

        $this->api = new OlhoVivo();
    }

    public function testClientOptionsAreSet(): void
    {
        $response = $this->api->clientOptions;

        $expectedKeys = ['base_uri', 'timeout', 'cookies', 'decode_content'];

        expect($response)
            ->toBeArray()
            ->toHaveKeys($expectedKeys);
    }

    public function testClientCanAuthenticate(): void
    {
        $response = $this->api->isAuthenticated;

        expect($response)
            ->toBeBool()
            ->toBeTrue();
    }

    public function testGetManyBusLinesWithValidParameter(): void
    {
        $response = $this->api->getManyBusLines('Lapa');

        $expectedProperties = ['lc', 'lt', 'sl', 'tl', 'tp', 'ts'];

        expect($this->api->getManyBusLines('Lapa'))
            ->toBeArray()
            ->and($response[0])
            ->toBeObject()
            ->toHaveProperties($expectedProperties);
    }

    public function testCannotGetManyBusLinesWithInvalidParameter(): void
    {
        $response = $this->api->getManyBusLines('wrong name');

        expect($response)
            ->toBeArray()
            ->toBeEmpty();
    }

    public function testGetBusLinesByDirectionWithValidParameter(): void
    {
        $responsewithMainTerminalDirection = $this->api->getBusLinesByDirection('Lapa', 1);

        $responseWithSecondaryTerminalDirection = $this->api->getBusLinesByDirection('Lapa', 2);

        $expectedProperties = ['cl', 'lc', 'lt', 'sl', 'tl', 'tp', 'ts'];

        expect($responsewithMainTerminalDirection)
            ->toBeArray()
            ->and($responsewithMainTerminalDirection[0])
            ->toBeObject()
            ->toHaveProperties($expectedProperties)
            ->and($responseWithSecondaryTerminalDirection[0])
            ->toBeObject()
            ->toHaveProperties($expectedProperties);
    }

    public function testCannotGetBusLinesByDirectionWithInvalidParameters(): void
    {
        $responsewithMainTerminalDirection = $this->api->getBusLinesByDirection('Lapa', 3);

        $responseWithSecondaryTerminalDirection = $this->api->getBusLinesByDirection('Lapa', 4);

        expect($responsewithMainTerminalDirection)
            ->toBeNull()
            ->and($responseWithSecondaryTerminalDirection)
            ->toBeNull();
    }

    public function testGetManyBusStopByAddressWithValidParameter(): void
    {
        $response = $this->api->getManyBusStopByAddress('Lapa');

        $expectedProperties = ['cp', 'np', 'ed', 'py', 'px'];

        expect($response)
            ->toBeArray()
            ->and($response[0])
            ->toBeObject()
            ->toHaveProperties($expectedProperties);
    }

    public function testCannotGetManyBusStopByAddressWithInvalidParameter(): void
    {
        $response = $this->api->getManyBusStopByAddress('wrong name');

        expect($response)
            ->toBeArray()
            ->toBeEmpty();
    }

    public function testGetManyBusStopByLineCodeWithValidParameter(): void
    {
        $response = $this->api->getManyBusStopByLineCode(2451);

        $expectedKeys = ['cp', 'np', 'ed', 'py', 'px'];

        expect($response)
            ->toBeArray()
            ->and($response[0])
            ->toHaveKeys($expectedKeys);
    }

    public function testCannotGetManyBusStopByLineCodeWithInvalidParameter(): void
    {
        $response = $this->api->getManyBusStopByLineCode(000);

        expect($response)
            ->toBeArray()
            ->toBeEmpty();
    }

    public function testGetManyBusStopsByLaneWithValidParameter(): void
    {
        $response = $this->api->getManyBusStopsByLaneCode(10);

        $expectedProperties = ['cp', 'np', 'ed', 'py', 'px'];

        expect($response)
            ->toBeArray()
            ->and($response[0])
            ->toBeObject()
            ->toHaveProperties($expectedProperties);
    }

    public function testCannotGetManyBusStopsByLaneWithInvalidParameter(): void
    {
        $response = $this->api->getManyBusStopsByLaneCode(0);

        expect($response)
            ->toBeArray()
            ->toBeEmpty();
    }

    public function testGetAllBusLanes(): void
    {
        $response = $this->api->getAllBusLanes();

        $expectedProperties = ['cc', 'nc'];

        expect($response)
            ->toBeArray()
            ->and($response[0])
            ->toBeObject()
            ->toHaveProperties($expectedProperties);
    }

    public function testGetAllBusCompanies(): void
    {
        $response = $this->api->getAllBusCompanies();

        $firstSetOfProperties = ['hr', 'e'];

        $secondSetOfProperties = ['a', 'e'];

        $thirdSetOfProperties = ['a', 'c', 'n'];

        expect($response)
            ->toBeObject()
            ->toHaveProperties($firstSetOfProperties)
            ->and($response->e[0])
            ->toBeObject()
            ->toHaveProperties($secondSetOfProperties)
            ->and($response->e[0]->e[0])
            ->toBeObject()
            ->toHaveProperties($thirdSetOfProperties);
    }

    public function testGetAllBusesPosition(): void
    {
        $response = $this->api->getAllBusesPosition();

        $firstSetOfProperties = ['hr', 'l'];

        $secondSetOfProperties = ['c', 'cl', 'sl', 'lt0', 'lt1', 'qv', 'vs'];

        expect($response)
            ->toBeObject()
            ->toHaveProperties($firstSetOfProperties)
            ->and($response->l[0])
            ->toBeObject()
            ->toHaveProperties($secondSetOfProperties);
    }

    public function testGetAllBusesByLineCodeWithValidParameter(): void
    {
        $response = $this->api->getAllBusesByLineCode(35201);

        $expectedProperties = ['hr', 'vs'];

        expect($response)
            ->toBeObject()
            ->toHaveProperties($expectedProperties);
    }

    public function testCannotGetAllBusesByLineCodeWithInvalidParameter(): void
    {
        $response = $this->api->getAllBusesByLineCode(00);

        $expectedProperties = ['hr', 'vs'];

        expect($response)
            ->toBeObject()
            ->toHaveProperties($expectedProperties)
            ->and($response->vs)
            ->toBeEmpty();
    }

    public function testGetManyBusesInGarageFromCompanyWithValidParameters(): void
    {
        $response = $this->api->getManyBusesInGarageFromCompany(230, 0);

        $firstSetOfProperties = ['hr', 'l'];

        $secondSetOfProperties = ['c', 'cl', 'sl', 'lt0', 'lt1', 'qv', 'vs'];

        $thirdSetOfProperties = ['p', 'a', 'ta', 'py', 'px', 'sv', 'is'];

        expect($response)
            ->toBeObject()
            ->toHaveProperties($firstSetOfProperties)
            ->and($response->l[0])
            ->toBeObject()
            ->toHaveProperties($secondSetOfProperties)
            ->and($response->l[0]->vs)
            ->toBeArray()
            ->and($response->l[0]->vs[0])
            ->toBeObject()
            ->toHaveProperties($thirdSetOfProperties);
    }

    public function testCannotGetManyBusesInGarageFromCompanyWithInvalidParameters(): void
    {
        $response = $this->api->getManyBusesInGarageFromCompany(0, 0);

        $expectedProperties = ['hr', 'l'];

        expect($response)
            ->toBeObject()
            ->toHaveProperties($expectedProperties)
            ->and($response->hr)
            ->toBeString()
            ->and($response->l)
            ->toBeEmpty();
    }

//    public function testGetArrivalPredictionByLineAndStop()
//    {
//        possiveis codigos stopCode 480014610, LineCode 34833
//        não foi possivel testar pelo horário em que estava sendo implementado este teste
//        $response = $this->api->getArrivalPredictionByLineAndStop(640001414, 2451);
//        expect($response)
//            ->toBeObject()
//            ->and($response[0])
//            ->toBeObject()
//            ->toHaveProperties(['cp', 'np', 'ed', 'py', 'px']);
//    }

//    public function testGetArrivalPredictionByStop()
//    {
//        stopCode 480014610
//        não foi possivel testar pelo horário em que estava sendo implementado este teste
//    }
}
