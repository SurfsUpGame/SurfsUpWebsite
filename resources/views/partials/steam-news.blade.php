<section class="py-16 bg-gray-800">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-white">Latest News from Steam</h2>
        
        <div id="steam-news-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- News items will be loaded here -->
            <div class="text-center text-gray-400 col-span-full">
                <i class="fas fa-spinner fa-spin text-4xl mb-4"></i>
                <p>Loading latest news...</p>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/steam-news')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('steam-news-container');
            container.innerHTML = '';
            
            if (data.appnews && data.appnews.newsitems) {
                data.appnews.newsitems.slice(0, 6).forEach(item => {
                    const date = new Date(item.date * 1000);
                    const formattedDate = date.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    
                    const newsCard = document.createElement('div');
                    newsCard.className = 'bg-gray-900 rounded-lg p-6 shadow-lg hover:shadow-xl transition-shadow duration-300';
                    newsCard.innerHTML = `
                        <h3 class="text-xl font-semibold text-white mb-2 line-clamp-2">${item.title}</h3>
                        <p class="text-sm text-gray-400 mb-4">
                            <i class="fas fa-calendar-alt mr-2"></i>${formattedDate}
                            ${item.author ? `<span class="ml-4"><i class="fas fa-user mr-2"></i>${item.author}</span>` : ''}
                        </p>
                        <div class="text-gray-300 line-clamp-4 mb-4">${stripHtml(item.contents)}</div>
                        <a href="${item.url}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center text-blue-400 hover:text-blue-300 transition-colors">
                            Read more <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    `;
                    container.appendChild(newsCard);
                });
            } else {
                container.innerHTML = '<p class="text-center text-gray-400 col-span-full">No news available at this time.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching Steam news:', error);
            document.getElementById('steam-news-container').innerHTML = 
                '<p class="text-center text-gray-400 col-span-full">Failed to load news. Please try again later.</p>';
        });
    
    function stripHtml(html) {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }
});
</script>