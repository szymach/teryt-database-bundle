<?php

namespace spec\FSi\Bundle\TerytDatabaseBundle\Teryt\Import;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use FSi\Bundle\TerytDatabaseBundle\Entity\PlaceType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PlacesDictionaryNodeConverterSpec extends ObjectBehavior
{
    function let(ObjectManager $om, ObjectRepository $or)
    {
        // It is not possible to mock internal classes with final constructor
        $this->beConstructedWith(new \SimpleXMLElement('<row></row>'), $om);
        $om->getRepository(Argument::type('string'))->willReturn($or);
        $or->findOneBy(Argument::type('array'))->willReturn();
    }

    function it_converts_node_to_places_dictionary_entry(ObjectManager $om)
    {
        $xml = <<<EOT
<row>
  <rm>02</rm>
  <nazwa_rm>kolonia                 </nazwa_rm>
  <stan_na>2013-02-28</stan_na>
</row>
EOT;

        $placeType = new PlaceType(2);
        $placeType->setName('kolonia');

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($placeType);
    }

    function it_converts_node_to_places_dictionary_entry_with_updating_existing_one(
        ObjectManager $om, ObjectRepository $or, PlaceType $placeType
    ){
        $xml = <<<EOT
<row>
  <rm>02</rm>
  <nazwa_rm>kolonia                 </nazwa_rm>
  <stan_na>2013-02-28</stan_na>
</row>
EOT;

        $or->findOneBy(array(
            'type' => 2
        ))->shouldBeCalled()->willReturn($placeType);

        $placeType->setName('kolonia')->shouldBeCalled()->willReturn($placeType);

        $this->beConstructedWith(new \SimpleXMLElement($xml), $om);
        $this->convertToEntity()->shouldBeLike($placeType);
    }
}
