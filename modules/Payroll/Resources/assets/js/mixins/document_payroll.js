export const documentPayrollMixin = {
    data() {
        return {
        }
    },
    methods: {
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

        // Vacaciones disfrutadas
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
        // Vacaciones disfrutadas


    }
}