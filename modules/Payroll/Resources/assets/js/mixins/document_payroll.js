export const documentPayrollMixin = {
    data() {
        return {
        }
    },
    methods: {
        // prima de servicio
        clickAddServiceBonus(){
            
            const salary_validation = this.salaryValidation()
            if(!salary_validation.success) return this.$message.warning(salary_validation.message)

            this.form.accrued.service_bonus.push({
                quantity :  0,
                payment :  0,
                paymentNS :  0,
            })

        },
        changePaymentNSServiceBonus(index){
            this.calculateTotal()
        },
        changePaymentServiceBonus(index){
            this.calculateTotal()
        },
        changeQuantityServiceBonus(index){
            
            this.setPaymentServiceBonus(index)
            this.calculateTotal()

        },
        setPaymentServiceBonus(index){
            this.form.accrued.service_bonus[index].payment = this.roundNumber((this.form.accrued.total_base_salary / this.quantity_days_year) * this.form.accrued.service_bonus[index].quantity)
        },
        clickCancelServiceBonus(index){
            this.form.accrued.service_bonus.splice(index, 1)
            this.calculateTotal()
        },
        recalculateServiceBonus(){

            this.form.accrued.service_bonus.forEach((element, index) => {
                this.setPaymentServiceBonus(index)
            })

        },
        // prima de servicio

        // cesantias
        clickAddSeverance(){

            const salary_validation = this.salaryValidation()
            if(!salary_validation.success) return this.$message.warning(salary_validation.message)

            this.form.accrued.severance.push({
                payment :  0,
                percentage :  0,
                interest_payment :  0,
            })

        },
        clickCancelSeverance(index){
            this.form.accrued.severance.splice(index, 1)
            this.calculateTotal()
        },
        calculateInterestPayment(index){
            this.form.accrued.severance[index].interest_payment = this.roundNumber(this.form.accrued.severance[index].payment * this.percentageToFactor(this.form.accrued.severance[index].percentage))
            this.calculateTotal()
        },
        // cesantias

        // bonificaciones
        clickAddBonuses(){

            this.form.accrued.bonuses.push({
                salary_bonus :  0,
                non_salary_bonus :  0,
            })

        },
        clickCancelBonuses(index){
            this.form.accrued.bonuses.splice(index, 1)
            this.calculateTotal()
        },
        changeSalaryBonus(index){
            this.calculateTotal()
        },
        // bonificaciones

        // vacaciones disfrutadas
        clickAddCommonVacation(){

            const salary_validation = this.salaryValidation()
            if(!salary_validation.success) return this.$message.warning(salary_validation.message)

            const quantity = 1
            const date_range = this.getStartEndDateRange(quantity)

            this.form.accrued.common_vacation.push({
                start_date : date_range[0],
                end_date : date_range[1],
                quantity :  quantity,
                payment :  0,
                start_end_date: date_range
            })

        },
        getStartEndDateRange(quantity){
            return [
                moment().format('YYYY-MM-DD'),
                moment().add(quantity, 'days').format('YYYY-MM-DD')
            ]
        }, 
        clickCancelCommonVacation(index){
            this.form.accrued.common_vacation.splice(index, 1)
            this.calculateTotal()
        },
        changeCommonVacationStartEndDate(index){
            
            const start_end_date = this.form.accrued.common_vacation[index].start_end_date
            const start_date = start_end_date[0]
            const end_date = start_end_date[1]
            this.form.accrued.common_vacation[index].quantity = this.roundNumber(moment(end_date, "YYYY-MM-DD").diff(moment(start_date, "YYYY-MM-DD"), 'days', true))

        },
        changePaymentCommonVacation(index){
            this.calculateTotal()
        },
        // vacaciones disfrutadas


        // vacaciones compensadas
        clickAddPaidVacation(){

            const salary_validation = this.salaryValidation()
            if(!salary_validation.success) return this.$message.warning(salary_validation.message)

            const quantity = 1
            const date_range = this.getStartEndDateRange(quantity)

            this.form.accrued.paid_vacation.push({
                start_date : date_range[0],
                end_date : date_range[1],
                quantity :  quantity,
                payment :  0,
                start_end_date: date_range
            })

        },
        clickCancelPaidVacation(index){
            this.form.accrued.paid_vacation.splice(index, 1)
            this.calculateTotal()
        },
        changePaidVacationStartEndDate(index){
            
            const start_end_date = this.form.accrued.paid_vacation[index].start_end_date
            const start_date = start_end_date[0]
            const end_date = start_end_date[1]
            this.form.accrued.paid_vacation[index].quantity = this.roundNumber(moment(end_date, "YYYY-MM-DD").diff(moment(start_date, "YYYY-MM-DD"), 'days', true))

        },
        changePaymentPaidVacation(index){
            this.calculateTotal()
        },
        // vacaciones compensadas

        // ayudas
        clickAddAid(){

            this.form.accrued.aid.push({
                salary_assistance :  0,
                non_salary_assistance :  0,
            })

        },
        clickCancelAid(index){
            this.form.accrued.aid.splice(index, 1)
            this.calculateTotal()
        },
        changeSalaryAid(index){
            this.calculateTotal()
        },
        // ayudas
        
        // otros conceptos
        clickAddOtherConcepts(){

            this.form.accrued.other_concepts.push({
                salary_concept :  0,
                non_salary_concept :  0,
                description_concept :  null,
            })

        },
        clickCancelOtherConcepts(index){
            this.form.accrued.other_concepts.splice(index, 1)
            this.calculateTotal()
        },
        changeSalaryOtherConcepts(index){
            this.calculateTotal()
        },
        // otros conceptos

    }
}