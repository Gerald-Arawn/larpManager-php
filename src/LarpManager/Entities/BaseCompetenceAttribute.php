<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 3.0.3 (doctrine2-annotation) on 2019-08-30 20:00:03.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

/**
 * LarpManager\Entities\CompetenceAttribute
 *
 * @Entity()
 * @Table(name="competence_attribute", indexes={@Index(name="fk_competence_has_attribute_type_attribute_type1_idx", columns={"attribute_type_id"}), @Index(name="fk_competence_has_attribute_type_competence1_idx", columns={"competence_id"})})
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"base":"BaseCompetenceAttribute", "extended":"CompetenceAttribute"})
 */
class BaseCompetenceAttribute
{
    /**
     * @Id
     * @Column(type="integer")
     */
    protected $competence_id;

    /**
     * @Id
     * @Column(type="integer")
     */
    protected $attribute_type_id;

    /**
     * @Column(name="`value`", type="integer")
     */
    protected $value;

    /**
     * @ManyToOne(targetEntity="Competence", inversedBy="competenceAttributes")
     * @JoinColumn(name="competence_id", referencedColumnName="id", nullable=false)
     */
    protected $competence;

    /**
     * @ManyToOne(targetEntity="AttributeType", inversedBy="competenceAttributes", cascade={"persist"})
     * @JoinColumn(name="attribute_type_id", referencedColumnName="id", nullable=false)
     */
    protected $attributeType;

    public function __construct()
    {
    }

    /**
     * Set the value of competence_id.
     *
     * @param integer $competence_id
     * @return \LarpManager\Entities\CompetenceAttribute
     */
    public function setCompetenceId($competence_id)
    {
        $this->competence_id = $competence_id;

        return $this;
    }

    /**
     * Get the value of competence_id.
     *
     * @return integer
     */
    public function getCompetenceId()
    {
        return $this->competence_id;
    }

    /**
     * Set the value of attribute_type_id.
     *
     * @param integer $attribute_type_id
     * @return \LarpManager\Entities\CompetenceAttribute
     */
    public function setAttributeTypeId($attribute_type_id)
    {
        $this->attribute_type_id = $attribute_type_id;

        return $this;
    }

    /**
     * Get the value of attribute_type_id.
     *
     * @return integer
     */
    public function getAttributeTypeId()
    {
        return $this->attribute_type_id;
    }

    /**
     * Set the value of value.
     *
     * @param integer $value
     * @return \LarpManager\Entities\CompetenceAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of value.
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Competence entity (many to one).
     *
     * @param \LarpManager\Entities\Competence $competence
     * @return \LarpManager\Entities\CompetenceAttribute
     */
    public function setCompetence(Competence $competence = null)
    {
        $this->competence = $competence;

        return $this;
    }

    /**
     * Get Competence entity (many to one).
     *
     * @return \LarpManager\Entities\Competence
     */
    public function getCompetence()
    {
        return $this->competence;
    }

    /**
     * Set AttributeType entity (many to one).
     *
     * @param \LarpManager\Entities\AttributeType $attributeType
     * @return \LarpManager\Entities\CompetenceAttribute
     */
    public function setAttributeType(AttributeType $attributeType = null)
    {
        $this->attributeType = $attributeType;

        return $this;
    }

    /**
     * Get AttributeType entity (many to one).
     *
     * @return \LarpManager\Entities\AttributeType
     */
    public function getAttributeType()
    {
        return $this->attributeType;
    }

    public function __sleep()
    {
        return array('competence_id', 'attribute_type_id', 'value');
    }
}