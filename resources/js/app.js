import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import '../css/app.css'

Alpine.plugin(collapse)

window.Alpine = Alpine

document.addEventListener('alpine:init', () => {
    Alpine.store('compare', {
        items: JSON.parse(localStorage.getItem('worxcars.comparison') || '[]'),
        toggle(id) {
            if (this.items.includes(id)) {
                this.items = this.items.filter((v) => v !== id)
            } else {
                if (this.items.length >= 4) {
                    this.items.shift()
                }
                this.items.push(id)
            }
            localStorage.setItem('worxcars.comparison', JSON.stringify(this.items))
        },
        has(id) {
            return this.items.includes(id)
        },
        clear() {
            this.items = []
            localStorage.setItem('worxcars.comparison', JSON.stringify(this.items))
        },
        link() {
            return this.items.length ? '/vehicles/compare?ids=' + this.items.join(',') : '#'
        },
    })
})

Alpine.start()