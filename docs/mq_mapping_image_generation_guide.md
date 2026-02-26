# MQ 맵핑 이미지 생성 가이드

## 📋 개요
- **목적**: 7세~14세 아동을 위한 MQ 맵핑 목표 썸네일 이미지 생성
- **모델**: Gemini Imagen 3 (nano banana)
- **스타일**: Pixar/Disney 3D 애니메이션
- **해상도**: 512x512px (정사각형)
- **총 개수**: 45개 (5개 카테고리 × 9개)

---

## 🎨 프롬프트 기본 구조

```
A Pixar style 3D rendered scene showing a {핵심 행동/상황} performed by a child (randomly boy or girl).
{구체적 장면 묘사 - 캐릭터, 환경, 소품}.
Art style: Pixar animation movie quality with smooth rendering and vibrant colors.
Composition: {구도 설명}.
Color palette: {구체적 색상 조합}.
Lighting: {조명 스타일}.
Background: {배경 환경}.
Mood: {감정/분위기}.
Important: Absolutely NO text, NO letters, NO numbers visible in the image.
Square format for thumbnail use.
```

**⚠️ 성별 다양성**: 모든 프롬프트에 "(randomly boy or girl)" 표현을 포함하여 성별이 랜덤하게 생성되도록 합니다.

---

## 📝 카테고리별 프롬프트 예시

### 1. Creation (창작) 카테고리

#### 1-1. "색종이로 종이접기 100개 만들기" (7세)
```
A Pixar style 3D rendered scene showing a cheerful young child sitting at a colorful table covered with vibrant origami papers and completed paper cranes, swans, and flowers scattered around, child holding up a completed origami creation with proud expression.
Art style: Pixar animation movie quality with smooth textures, rounded shapes, and expressive character face.
Composition: Medium shot from slightly elevated angle, child centered at table with origami pieces creating visual interest.
Color palette: Rainbow origami papers - red, blue, yellow, green, pink papers, warm wooden table, soft room lighting.
Lighting: Warm indoor afternoon sunlight streaming through window, creating soft shadows and highlighting colorful papers.
Background: Cozy home craft area with shelves, art supplies visible but soft-focused, inviting creative space.
Mood: Joyful, creative, accomplishment and pride in making things.
Important: Absolutely NO text, NO Korean characters, NO numbers, NO letters visible anywhere in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 1-2. "일기장에 그림일기 30개 쓰기" (10세)
```
A Pixar style 3D rendered scene showing a happy elementary school kid sitting at a desk with an open diary, drawing colorful illustrations with crayons, previous diary pages visible showing cute drawings, warm desk lamp illuminating the workspace.
Art style: Pixar animation style with warm lighting, smooth surfaces, and child-friendly details.
Composition: Close-up shot from side angle showing child's focused expression and the diary page being created.
Color palette: Warm study colors - brown wooden desk, colorful crayons, white diary pages with rainbow drawings, yellow lamp light.
Lighting: Focused warm desk lamp creating cozy study atmosphere, soft evening indoor lighting.
Background: Comfortable bedroom study corner with bookshelf, window showing evening sky, personal learning space.
Mood: Focused, creative, peaceful evening journaling time.
Important: Absolutely NO text, NO Korean words, NO diary text, NO numbers visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 1-3. "네이버 블로그에 웹툰 10편 연재하기" (14세)
```
A Pixar style 3D rendered scene showing a determined teenage youth sitting at a digital drawing tablet, stylus in hand creating cartoon characters on the screen, multiple comic panel sketches displayed on the tablet, energetic creative workspace with inspiration boards.
Art style: Disney Pixar quality with modern tech aesthetic, smooth character animation, vibrant digital art tools.
Composition: Medium shot from side angle showing both the teen and their digital workspace, dynamic creative energy.
Color palette: Modern creator colors - sleek black tablet, glowing blue screen light, colorful comic sketches, purple LED mood lighting.
Lighting: Screen glow illuminating teen's focused face, purple LED strips creating modern content creator atmosphere.
Background: Contemporary bedroom studio with posters, art references, shelves with figures, inspiring creative environment.
Mood: Determined, artistic, modern digital creator empowerment.
Important: Absolutely NO text, NO blog names, NO Korean characters, NO episode numbers, NO UI elements visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 1-4. "유튜버가 되어 영상 만들기" (10세)
```
A Pixar style 3D rendered scene showing an enthusiastic child sitting in a colorful home studio setup with a camera on tripod pointed at them, ring lights glowing, and cheerful decorations in the background, child making excited hand gestures.
Art style: Pixar animation style with vibrant saturated colors and energetic character expression.
Composition: Medium shot showing both the child and their mini studio setup, creating a complete creator space vibe.
Color palette: Bright creator colors - pink and purple LED lights, yellow walls, red camera, child wearing blue outfit.
Lighting: Multiple light sources - warm ring light from front, colorful LED strips creating energetic atmosphere.
Background: Cozy bedroom studio with posters, plants, shelves with toys, typical kid YouTuber setup.
Mood: Energetic, creative, modern content creator excitement.
Important: Absolutely NO text, NO channel names, NO subscriber counts, NO Korean words, NO brand names visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 1-5. "작곡 앱으로 노래 3곡 만들기" (14세)
```
A Pixar style 3D rendered scene showing a teenage youth wearing headphones sitting at a desk with a laptop and MIDI keyboard, musical notes and sound waves floating magically in the air around them, excited expression while creating music.
Art style: Pixar animation quality with magical musical effects and modern tech aesthetic.
Composition: Medium shot capturing both the teen and their music production setup, floating musical elements adding depth.
Color palette: Musical creative colors - glowing cyan and purple sound waves, black keyboard, warm laptop screen glow, orange musical notes.
Lighting: Soft studio lighting with magical glow from floating musical elements, intimate creative atmosphere.
Background: Modern bedroom music studio with acoustic foam panels, posters of musicians, inspiring production space.
Mood: Inspired, artistic, magical music creation moment.
Important: Absolutely NO text, NO Korean characters, NO song titles, NO music software UI, NO brand names visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

---

### 2. Adventure (탐험) 카테고리

#### 2-1. "동물원에서 동물 10종류 보기" (7세)
```
A Pixar style 3D rendered scene showing a cheerful young child standing at a colorful zoo entrance, surrounded by friendly cartoon animals including an elephant, giraffe, and lion gathered around the child in a welcoming circle.
Art style: Pixar animation movie quality with smooth textures, rounded shapes, and expressive character faces.
Composition: Medium shot with child in center, animals positioned around forming a friendly group.
Color palette: Warm sunny colors - bright blue sky, green grass, orange-brown animals, child wearing red shirt.
Lighting: Bright outdoor daylight with soft shadows, cheerful morning atmosphere.
Background: Simple zoo entrance gate with trees, cloudy sky, soft-focused environment.
Mood: Joyful, excited, child-friendly adventure feeling.
Important: Absolutely NO text, NO letters, NO Korean characters, NO numbers visible anywhere in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 2-2. "우리나라 5개 도시 여행 가기" (10세)
```
A Pixar style 3D rendered scene showing a happy elementary school kid with a colorful backpack standing on a train platform, holding a travel map, with a modern Korean train visible in the background, excited expression ready for adventure.
Art style: Pixar animation style with smooth rendering and travel adventure energy.
Composition: Medium shot of child from slight low angle, train platform creating depth in background.
Color palette: Travel colors - bright orange backpack, blue train, gray platform, yellow safety line, clear sky.
Lighting: Bright sunny morning light creating optimistic travel atmosphere, clear shadows.
Background: Modern clean train station platform with train, simple urban travel environment.
Mood: Excited, adventurous, ready to explore new places.
Important: Absolutely NO text, NO Korean city names, NO station signs, NO train numbers visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 2-3. "제주 올레길 1코스 완주하기" (14세)
```
A Pixar style 3D rendered scene showing a teenage hiker with a backpack walking on a beautiful coastal path, with dramatic ocean cliffs and blue sea waves visible in the background, orange ribbon trail markers visible on the path.
Art style: Disney Pixar quality outdoor adventure scene with detailed environment and atmospheric depth.
Composition: Wide cinematic shot from behind and side of hiker, showing both character and stunning landscape.
Color palette: Natural beauty - deep blue ocean, orange trail markers, green coastal vegetation, warm skin tones.
Lighting: Bright sunny day with clear blue sky, strong sunlight creating vibrant colors and defined shadows.
Background: Spectacular Jeju island coastal scenery with rocky cliffs, ocean horizon, scattered clouds.
Mood: Inspiring, adventurous, breathtaking natural beauty.
Important: Absolutely NO text, NO trail signs with Korean, NO distance markers, NO route numbers visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 2-4. "바닷가에서 조개껍데기 20개 줍기" (7세)
```
A Pixar style 3D rendered scene showing a cheerful young child crouching at a sandy beach, collecting colorful seashells and putting them in a small bucket, gentle waves in the background, seashells scattered on the sand around the child.
Art style: Pixar animation quality with soft textures, rounded shapes, and warm beach atmosphere.
Composition: Close-up shot from side angle showing child's delighted expression and collection activity.
Color palette: Beach vacation colors - golden sand, turquoise water, white foam waves, pink and white seashells, child in yellow swimwear.
Lighting: Warm sunny beach lighting with soft shadows, late morning golden hour glow.
Background: Beautiful sandy beach with gentle waves, clear blue sky, peaceful ocean horizon.
Mood: Peaceful, joyful, summer vacation discovery.
Important: Absolutely NO text, NO Korean characters, NO numbers, NO beach signs visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 2-5. "1박 2일 캠핑하면서 텐트에서 자기" (10세)
```
A Pixar style 3D rendered scene showing a happy elementary school kid sitting in front of an orange camping tent at a forest campsite, campfire glowing nearby, starry night sky above, backpack and camping gear arranged around the tent.
Art style: Pixar animation style with magical evening atmosphere and warm camping vibes.
Composition: Medium wide shot showing tent, child, and campfire creating cozy triangle composition.
Color palette: Camping night colors - orange tent, warm campfire glow, dark blue night sky, green grass, yellow firelight.
Lighting: Dual lighting from campfire creating warm orange glow and cool moonlight from above.
Background: Forest campsite with trees silhouetted against starry sky, peaceful natural setting.
Mood: Cozy, adventurous, peaceful outdoor night experience.
Important: Absolutely NO text, NO Korean characters, NO camping site names, NO tent brand names visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

---

### 3. Challenge (도전) 카테고리

#### 3-1. "자전거 보조바퀴 떼고 타기" (7세)
```
A Pixar style 3D rendered scene showing a cheerful young child riding a red bicycle without training wheels on a park path, arms slightly out for balance, big smile of accomplishment, parent visible in background cheering.
Art style: Pixar animation movie quality with dynamic motion and triumphant energy.
Composition: Side tracking shot capturing motion and accomplishment, child slightly off-center creating dynamic feel.
Color palette: Celebration colors - bright red bicycle, blue helmet, green park grass, clear sky, child wearing yellow shirt.
Lighting: Bright sunny afternoon light creating clear shadows, energetic outdoor atmosphere.
Background: Beautiful park path with trees, flowers, open space suggesting freedom and achievement.
Mood: Triumphant, brave, milestone achievement moment.
Important: Absolutely NO text, NO Korean characters, NO bike brand names, NO numbers visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 3-2. "학교 달리기 대회에서 3등 안에 들기" (10세)
```
A Pixar style 3D rendered scene showing a determined elementary school kid running with intense focus, wearing athletic clothes and running bib, other runners slightly blurred in background, race track environment.
Art style: Pixar animation style with dynamic motion blur and athletic energy.
Composition: Dynamic low angle shot capturing speed and determination, forward momentum emphasized.
Color palette: Athletic colors - bright red running outfit, green track, blue sky, white running shoes.
Lighting: Bright outdoor stadium lighting, strong sunlight creating dramatic shadows suggesting speed.
Background: School athletic track with lanes, bleachers with spectators soft-focused, competitive atmosphere.
Mood: Determined, focused, intense competition moment.
Important: Absolutely NO text, NO Korean characters, NO race numbers, NO bib numbers, NO school names visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 3-3. "하프 마라톤 완주하기" (14세)
```
A Pixar style 3D rendered scene showing a determined teenage runner in athletic wear crossing an invisible finish line with raised arms in celebration, with colorful confetti particles floating in the air.
Art style: Disney Pixar animation quality with smooth character modeling and dynamic pose.
Composition: Dynamic low-angle shot capturing the triumphant moment, runner slightly off-center creating motion.
Color palette: Energetic colors - bright red running outfit, orange-yellow confetti, blue sky background.
Lighting: Dramatic golden hour sunlight from behind creating rim lighting on the character's silhouette.
Background: Blurred outdoor track environment with trees, suggesting daytime athletic event.
Mood: Triumphant, energetic, inspiring achievement moment.
Important: Absolutely NO text, NO letters, NO Korean characters, NO distance numbers, NO race bibs visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 3-4. "전국 과학 경진대회 본선 진출하기" (14세)
```
A Pixar style 3D rendered scene showing a focused teenage youth presenting a science project with a robot or science experiment on a table, wearing semi-formal attire, excited expression while demonstrating invention to invisible judges.
Art style: Pixar animation quality with academic competition atmosphere and innovative energy.
Composition: Medium shot showing teen and their impressive science project, both in focus creating achievement composition.
Color palette: STEM colors - white lab coat or blue button-up shirt, metallic gray robot, colorful experiment materials, professional setting.
Lighting: Bright indoor competition lighting with spotlight effect highlighting the project and presenter.
Background: Professional competition venue with presentation boards, science fair atmosphere, formal event setting.
Mood: Confident, intelligent, proud scientific achievement.
Important: Absolutely NO text, NO Korean characters, NO project titles, NO science fair banners, NO numbers visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 3-5. "로봇 코딩 대회에서 입상하기" (14세)
```
A Pixar style 3D rendered scene showing a teenage youth holding a colorful small robot they built, standing at a robotics competition table with obstacle course visible, excited proud expression, other robots and competition elements in background.
Art style: Disney Pixar animation style with tech competition energy and youthful innovation.
Composition: Medium shot from slight low angle making teen and robot look heroic and accomplished.
Color palette: Tech competition colors - blue and orange robot, gray competition mat, red obstacles, teen wearing team shirt.
Lighting: Bright indoor event lighting with dynamic highlights on the shiny robot surface.
Background: Robotics competition venue with tables, challenge courses, modern STEM education environment.
Mood: Proud, accomplished, tech-savvy achievement celebration.
Important: Absolutely NO text, NO Korean characters, NO team names, NO competition banners, NO robot names visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

---

### 4. Growth (성장) 카테고리

#### 4-1. "한글 받침까지 완벽하게 읽기" (7세)
```
A Pixar style 3D rendered scene showing a cheerful young child sitting on a colorful cushion holding an open Korean children's book, reading aloud with confident expression, Korean alphabet chart visible on wall in background, warm learning environment.
Art style: Pixar animation movie quality with warm educational atmosphere and encouraging vibes.
Composition: Close-up shot showing child's proud reading expression and the open book, creating intimate learning moment.
Color palette: Warm learning colors - yellow cushion, colorful book illustrations, soft pink walls, natural wood bookshelf.
Lighting: Soft warm indoor reading light, cozy afternoon study time atmosphere.
Background: Child-friendly learning space with educational posters, alphabet decorations, bookshelf with children's books.
Mood: Accomplished, confident, milestone literacy achievement.
Important: Absolutely NO readable Korean text, NO Hangul characters that can be read, NO letters, NO numbers visible clearly in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 4-2. "영어 단어 500개 외우기" (10세)
```
A Pixar style 3D rendered scene showing a focused elementary school kid sitting at a study desk with English vocabulary flashcards spread out, holding up one card with triumphant expression, colorful word bubbles floating magically around their head.
Art style: Pixar animation style with educational magic effects and achievement energy.
Composition: Medium shot from front angle showing study setup and floating vocabulary elements creating depth.
Color palette: Educational colors - blue study desk, colorful flashcards (pink, yellow, green), white word bubbles, warm room lighting.
Lighting: Focused desk lamp lighting with magical glow from floating word bubbles, studious atmosphere.
Background: Organized study room with bookshelf, world map poster, comfortable learning environment.
Mood: Accomplished, smart, rewarding study achievement.
Important: Absolutely NO readable English words, NO Korean text, NO actual vocabulary visible, NO numbers in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 4-3. "파이썬으로 웹 크롤링 프로그램 만들기" (14세)
```
A Pixar style 3D rendered scene showing a determined teenage youth sitting at a desk with dual monitors displaying colorful code editor and data visualizations, wearing headphones, typing on keyboard with focused concentration, Python snake mascot hologram floating beside them.
Art style: Disney Pixar animation quality with modern tech aesthetic and programming energy.
Composition: Medium shot from side angle showing teen, monitors, and holographic Python mascot creating tech composition.
Color palette: Programming colors - blue and green code on screens, yellow Python mascot, dark desk setup, purple LED accent lighting.
Lighting: Screen glow illuminating teen's focused face, cool blue tech lighting with purple accents.
Background: Modern tech workspace with coding posters, bookshelf with programming books, professional developer atmosphere.
Mood: Focused, intelligent, empowered young programmer.
Important: Absolutely NO readable code, NO Korean characters, NO English text, NO program names, NO variable names visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 4-4. "코딩 교육 30시간 수료하기" (10세)
```
A Pixar style 3D rendered scene showing a happy elementary school kid sitting at a colorful computer with block-based coding interface on screen, robot character on screen responding to the code, kid raising arms in celebration of working program.
Art style: Pixar animation style with educational tech environment and success celebration.
Composition: Medium shot showing both kid and computer screen with visible programming success, achievement moment.
Color palette: Educational tech colors - bright blue computer, colorful code blocks on screen, orange robot character, green desk.
Lighting: Screen glow creating excitement, bright classroom lighting, celebratory atmosphere.
Background: Computer lab or classroom with other computers visible, learning technology environment.
Mood: Accomplished, excited, empowered young coder.
Important: Absolutely NO readable code, NO Korean text, NO English instructions, NO UI text, NO hour numbers visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 4-5. "책 50권 읽기" (10세)
```
A Pixar style 3D rendered scene showing a happy elementary school kid sitting in a cozy reading nook surrounded by tall stacks of colorful children's books, holding an open book with engaged expression, magical sparkles floating from the pages.
Art style: Pixar animation quality with magical reading atmosphere and literary adventure vibes.
Composition: Medium shot with kid centered among book towers, creating cozy surrounded-by-books feeling.
Color palette: Library colors - rainbow book spines (red, blue, yellow, green, purple), warm wood tones, soft beige reading cushion.
Lighting: Warm afternoon sunlight streaming through window, magical sparkles adding enchantment to reading.
Background: Cozy home library corner with bookshelf, window seat, comfortable reading atmosphere.
Mood: Engaged, curious, magical literary journey.
Important: Absolutely NO readable book titles, NO Korean text, NO English words, NO author names, NO page numbers visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

---

### 5. Experience (경험) 카테고리

#### 5-1. "롯데월드에서 놀이기구 5개 타기" (7세)
```
A Pixar style 3D rendered scene showing a cheerful young child on a colorful carousel ride, sitting on a vibrant painted horse, arms raised in excitement, other carousel animals visible around, magical amusement park atmosphere with lights.
Art style: Pixar animation movie quality with vibrant carnival colors and joyful energy.
Composition: Medium shot capturing child's excited expression and carousel movement, slight motion blur suggesting ride in action.
Color palette: Carnival colors - bright red, yellow, and blue carousel horses, golden lights, pastel pink and purple background.
Lighting: Magical evening carnival lighting with golden warm bulbs creating festive atmosphere.
Background: Amusement park setting with soft-focused rides, lights, and festive decorations.
Mood: Joyful, exciting, magical theme park experience.
Important: Absolutely NO text, NO Korean characters, NO park names, NO ride names, NO signage visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 5-2. "학급 반장 되어보기" (10세)
```
A Pixar style 3D rendered scene showing a confident elementary school kid standing at the front of a classroom, gesturing while speaking to classmates seated at desks, wearing a proud smile, classroom environment with windows and educational decorations.
Art style: Pixar animation style with school leadership atmosphere and confident energy.
Composition: Medium shot from slightly low angle showing kid as classroom leader, classmates visible creating context.
Color palette: School colors - blue kid's outfit, wooden desks, white walls with colorful educational posters, green chalkboard.
Lighting: Bright classroom natural lighting from windows, clear and confident atmosphere.
Background: Typical Korean classroom with rows of desks, students, windows, educational environment.
Mood: Confident, responsible, proud leadership moment.
Important: Absolutely NO text, NO Korean characters, NO chalkboard writing, NO posters with readable text, NO classroom signs visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 5-3. "학생회 임원으로 활동하기" (14세)
```
A Pixar style 3D rendered scene showing a confident teenage youth standing with a group of diverse student council members in a school hallway, holding folders and materials, discussing plans together, leadership atmosphere.
Art style: Disney Pixar animation quality with teamwork energy and youthful leadership vibes.
Composition: Medium group shot showing teen in center as leader with council members, collaborative positioning.
Color palette: School leadership colors - navy blazer uniforms, white shirts, colorful folders, bright hallway lighting.
Lighting: Bright school hallway natural lighting creating clean and professional student leadership atmosphere.
Background: Modern school hallway with lockers, bulletin boards, windows, active school environment.
Mood: Responsible, collaborative, empowered student leadership.
Important: Absolutely NO text, NO Korean characters, NO school names, NO posters with text, NO locker numbers visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 5-4. "K-POP 콘서트 직관 가기" (10세)
```
A Pixar style 3D rendered scene showing an excited elementary school kid in a concert audience holding a light stick, wearing merchandise, surrounded by other fans, stage with colorful lights visible in background, pure joy expression.
Art style: Pixar animation style with concert energy and fandom excitement atmosphere.
Composition: Close-up shot of kid with light stick raised, stage lights creating dynamic background bokeh.
Color palette: Concert colors - bright neon stage lights (pink, purple, blue), glowing light stick, dark audience, colorful merchandise.
Lighting: Dynamic stage lighting from behind creating colorful bokeh effect, light stick glow illuminating excited face.
Background: Concert venue with stage lights, blurred audience, energetic K-pop concert atmosphere.
Mood: Thrilled, excited, dream concert experience.
Important: Absolutely NO text, NO Korean characters, NO artist names, NO fan banners with text, NO merchandise text visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

#### 5-5. "봉사활동 50시간 채우기" (14세)
```
A Pixar style 3D rendered scene showing a warm-hearted teenage youth wearing an apron serving food at a community center, handing a meal tray to an elderly person with a kind smile, volunteer atmosphere with other helpers in background.
Art style: Disney Pixar animation quality with warm community service atmosphere and compassionate vibes.
Composition: Medium shot showing meaningful interaction between teen volunteer and community member, human connection emphasized.
Color palette: Warm community colors - orange apron, white serving tray, soft beige community center walls, warm food colors.
Lighting: Soft warm indoor lighting creating caring and welcoming community atmosphere.
Background: Community center or soup kitchen with serving counter, tables, other volunteers, helpful environment.
Mood: Compassionate, fulfilling, meaningful community service.
Important: Absolutely NO text, NO Korean characters, NO organization names, NO apron text, NO time records visible in the image.
Square format optimized for thumbnail icon (512x512px).
```

---

## ✅ 프롬프트 작성 체크리스트

생성하기 전 반드시 확인:

### 필수 포함 요소
- [ ] "A Pixar style 3D rendered scene" 시작
- [ ] 연령대에 맞는 캐릭터 표현 (young child / elementary school kid / teenage youth)
- [ ] 구체적인 행동/상황 묘사
- [ ] Art style 명시
- [ ] Composition (구도) 설명
- [ ] Color palette (3-5개 구체적 색상)
- [ ] Lighting (조명 스타일과 방향)
- [ ] Background (환경 설명)
- [ ] Mood (감정/분위기)
- [ ] "Absolutely NO text, NO Korean characters" 강조
- [ ] "Square format... 512x512px" 명시

### 피해야 할 요소
- [ ] ❌ 부정문 과다 사용 (원하는 것 중심으로)
- [ ] ❌ 추상적 표현 ("beautiful", "nice")
- [ ] ❌ 복잡한 배경 (단순하게)
- [ ] ❌ 읽을 수 있는 텍스트 언급
- [ ] ❌ 구체적 브랜드/회사 이름

---

## 🎯 연령별 특징 정리

### 7세 (유치원생)
- **캐릭터**: cheerful young child (randomly boy or girl)
- **행동**: 단순하고 직관적 (접기, 줍기, 놀기)
- **색상**: 밝고 원색적 (빨강, 파랑, 노랑)
- **분위기**: 매우 밝고 안전한 느낌

### 10세 (초등 저학년)
- **캐릭터**: happy elementary school kid (randomly boy or girl)
- **행동**: 학습과 취미 혼합 (읽기, 코딩, 여행)
- **색상**: 다채롭고 활기찬 (다양한 색 조합)
- **분위기**: 호기심과 성장 느낌

### 14세 (중학생)
- **캐릭터**: determined teenage youth (randomly boy or girl)
- **행동**: 전문적이고 목표지향적 (대회, 자격증, 도전)
- **색상**: 세련되고 강렬한 (대비 강조)
- **분위기**: 성취와 독립성 느낌

---

## 📂 이미지 저장 규칙

생성된 이미지 파일명 형식:
```
mapping_{category}_{age}_{index}.png

예시:
- mapping_creation_7_001.png
- mapping_adventure_10_002.png
- mapping_challenge_14_003.png
```

저장 경로:
```
/home/imgeongi/apps/mqway/html/storage/app/public/uploads/mapping/
```

---

## 🔄 DB 업데이트 쿼리

이미지 생성 후 DB 업데이트:

```sql
-- 예시: creation 카테고리 첫 번째 항목
UPDATE mq_mapping_item
SET mq_image = 'mapping_creation_7_001.png',
    mq_update_date = NOW()
WHERE idx = 1;
```

---

## 📊 진행 체크리스트

### Creation (창작) - 9개
- [ ] 1. 색종이로 종이접기 100개 만들기 (7세)
- [ ] 2. 그림 그려서 냉장고에 10개 붙이기 (7세)
- [ ] 3. 레고로 우리 집 만들기 (7세)
- [ ] 4. 일기장에 그림일기 30개 쓰기 (10세)
- [ ] 5. 스크래치로 간단한 게임 만들기 (10세)
- [ ] 6. 유튜브에 내가 만든 영상 올리기 (10세)
- [ ] 7. 네이버 블로그에 웹툰 10편 연재하기 (14세)
- [ ] 8. 유니티로 3D 게임 1개 완성하기 (14세)
- [ ] 9. 작곡 앱으로 노래 3곡 만들기 (14세)

### Adventure (탐험) - 9개
- [ ] 1. 동물원에서 동물 10종류 보기 (7세)
- [ ] 2. 바닷가에서 조개껍데기 20개 줍기 (7세)
- [ ] 3. 할머니 집에 혼자 가보기 (7세)
- [ ] 4. 우리나라 5개 도시 여행 가기 (10세)
- [ ] 5. 한라산 백록담까지 올라가기 (10세)
- [ ] 6. 1박 2일 캠핑하면서 텐트에서 자기 (10세)
- [ ] 7. 제주 올레길 1코스 완주하기 (14세)
- [ ] 8. 일본 도쿄 자유여행 가기 (14세)
- [ ] 9. 스쿠버다이빙 오픈워터 자격증 따기 (14세)

### Challenge (도전) - 9개
- [ ] 1. 자전거 보조바퀴 떼고 타기 (7세)
- [ ] 2. 수영장에서 물에 얼굴 담그기 (7세)
- [ ] 3. 구구단 2단~5단 외우기 (7세)
- [ ] 4. 학교 달리기 대회에서 3등 안에 들기 (10세)
- [ ] 5. 피아노 발표회에서 연주하기 (10세)
- [ ] 6. 주산 3급 자격증 따기 (10세)
- [ ] 7. 하프 마라�on(21km) 완주하기 (14세)
- [ ] 8. 전국 과학 경진대회 본선 진출하기 (14세)
- [ ] 9. 로봇 코딩 대회에서 입상하기 (14세)

### Growth (성장) - 9개
- [ ] 1. 한글 받침까지 완벽하게 읽기 (7세)
- [ ] 2. 동화책 20권 읽고 부모님께 이야기하기 (7세)
- [ ] 3. 혼자서 신발 끈 묶기 (7세)
- [ ] 4. 영어 단어 500개 외우기 (10세)
- [ ] 5. 코딩 교육 30시간 수료하기 (10세)
- [ ] 6. 위인전 전집 10권 읽기 (10세)
- [ ] 7. 토익 600점 이상 받기 (14세)
- [ ] 8. 파이썬으로 웹 크롤링 프로그램 만들기 (14세)
- [ ] 9. 한국사능력검정시험 3급 합격하기 (14세)

### Experience (경험) - 9개
- [ ] 1. 롯데월드에서 놀이기구 5개 타기 (7세)
- [ ] 2. 뮤지컬 공연 보러 가기 (7세)
- [ ] 3. 친구 생일파티에 초대받기 (7세)
- [ ] 4. 학급 반장 되어보기 (10세)
- [ ] 5. K-POP 콘서트 직관 가기 (10세)
- [ ] 6. 강아지 키우면서 산책시키기 (10세)
- [ ] 7. 학생회 임원으로 활동하기 (14세)
- [ ] 8. 봉사활동 50시간 채우기 (14세)
- [ ] 9. 영화 시사회 시사단 참여하기 (14세)

---

## 💡 팁

1. **일관성 유지**: 같은 카테고리 내에서 스타일 통일
2. **연령 구분**: 캐릭터 표현을 연령에 맞게 조정
3. **텍스트 제거**: "NO text" 강조로 한글 깨짐 방지
4. **배경 단순화**: 썸네일에서는 배경보다 주요 행동 강조
5. **색상 일관성**: 카테고리별 색상 팔레트 유지

---

**생성 일자**: 2025-10-14
**작성자**: Claude (Sonnet 4.5)
**버전**: 1.0
